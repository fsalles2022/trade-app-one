<?php

namespace McAfee\Tests\Unit\Services;

use Gateway\tests\Helpers\GatewayFactoriesHelper;
use McAfee\Enumerators\McAfeeStatus;
use McAfee\Models\McAfeeMobileSecurity;
use McAfee\Services\McAfeeSaleAssistance;
use McAfee\Tests\Helpers\McAfeeFactoriesHelper;
use Mockery;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class McAfeeSaleAssistanceTest extends TestCase
{
    use McAfeeFactoriesHelper, GatewayFactoriesHelper;

    private $service;

    protected function setUp()
    {
        parent::setUp();
        $service       = $this->mcAfeeFactories()->of(McAfeeMobileSecurity::class)->make();
        $sale          = (new SaleBuilder())->withServices([$service])->build();
        $this->service = $sale->services->first();
    }

    /** @test */
    public function should_return_correct_message_when_parameters_is_valid()
    {
        $mcAfeeSaleAssistance = resolve(McAfeeSaleAssistance::class);

        $content = $mcAfeeSaleAssistance->integrateService(
            $this->service,
            ['creditCard' => $this->payloadCreditCard()]
        );

        $this->assertEquals($content['message'], trans('mcAfee::messages.subscription.success', ['label' => $this->service->label]));
    }

    /** @test */
    public function should_persist_data_when_service_is_activated()
    {
        $received = $this->assistance()->integrateService($this->service, [
            'creditCard' => $this->payloadCreditCard()
            ]);

        $this->assertDatabaseHas(
            'sales',
            [
                'services.serviceTransaction'           => $this->service->serviceTransaction,
                'services.status'                       => ServiceStatus::APPROVED,
                'services.license.mcAfeeReference'      => 'NCS1230361',
                'services.license.mcAfeeActivationCode' => 'RGYABC',
                'services.license.mcAfeeProductKey'     => 'r0EHYt5McOPElSK2hUEsvr2yB3S/xbHcR7XXBPqtKJXZqDP/3p9KV29wnmQqs+wi',
            ],
            'mongodb'
        );
    }

    /** @test */
    public function should_call_trial_sale()
    {
        $card = ['creditCard' => $this->payloadCreditCard()];

        $mock = Mockery::mock(McAfeeSaleAssistance::class)->makePartial();
        $mock->shouldReceive('isTrial')->once()->andReturnTrue();
        $mock->shouldReceive('trialSale')->once();
        $mock->shouldReceive('defaultSale')->never();

        $mock->shouldReceive('integrateService')
            ->with(['service' => $this->service, 'payload' => $card]);

        $received = $mock->integrateService($this->service, $card);

        $expectedResponse = ['message' => trans('mcAfee::messages.subscription.trial_success', ['label' => $this->service->label])];
        $this->assertEquals($expectedResponse, $received);
    }

    /** @test */
    public function should_call_default_sale()
    {
        $card = ['creditCard' => $this->payloadCreditCard()];

        $mock = Mockery::mock(McAfeeSaleAssistance::class)->makePartial();
        $mock->shouldReceive('isTrial')->once()->andReturnFalse();
        $mock->shouldReceive('trialSale')->never();
        $mock->shouldReceive('defaultSale')->once();

        $mock->shouldReceive('integrateService')
            ->with(['service' => $this->service, 'payload' => $card]);

        $received = $mock->integrateService($this->service, $card);

        $expectedResponse = ['message' => trans('mcAfee::messages.subscription.success', ['label' => $this->service->label])];
        $this->assertEquals($expectedResponse, $received);
    }

    /** @test */
    public function should_return_false_when_is_not_trial()
    {
        unset($this->service['license']);

        $received = $this->assistance()->isTrial($this->service);
        $this->assertFalse($received);

        $this->service->setAttribute('license', [
            'trial' => [
                'status' => McAfeeStatus::ONGOING
            ]
        ]);

        $received2 = $this->assistance()->isTrial($this->service);
        $this->assertFalse($received2);
    }

    /** @test */
    public function should_return_true_when_is_trial()
    {
        $this->service->setAttribute('license', [
            'trial' => true
        ]);

        $this->assertTrue($this->assistance()->isTrial($this->service));

        $this->service->setAttribute('license', [
            'trial' => 1
        ]);

        $this->assertTrue($this->assistance()->isTrial($this->service));
    }

    private function assistance(): McAfeeSaleAssistance
    {
        return resolve(McAfeeSaleAssistance::class);
    }
}
