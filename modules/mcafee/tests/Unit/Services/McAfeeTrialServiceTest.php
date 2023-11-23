<?php

namespace McAfee\Tests\Unit\Services;

use Carbon\Carbon;
use Gateway\API\Gateway;
use Gateway\Connection\GatewayConnection;
use Gateway\Enumerators\GatewayStatus;
use Gateway\Exceptions\GatewayExceptions;
use Gateway\Helpers\GatewayMethodsEnum;
use Gateway\Services\GatewayService;
use Gateway\Tests\ServerTest\Methods\Tokenize;
use McAfee\Enumerators\McAfeeStatus;
use McAfee\Models\McAfeeMobileSecurity;
use McAfee\Services\McAfeeTrialService;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class McAfeeTrialServiceTest extends TestCase
{
    /** @test */
    public function should_call_trial_sale_(): void
    {
        $service = factory(McAfeeMobileSecurity::class)->make();
        $sale    = (new SaleBuilder())->withServices([$service])->build();

        $gatewayServer = \Mockery::mock(Gateway::class)->makePartial();

        $mockGateway = \Mockery::mock(GatewayService::class)->makePartial();
        $mockGateway->shouldReceive('tokenize')->once()->andReturn($service);
        $mockGateway->shouldReceive('authorize')->once()->andReturn($gatewayServer);
        $this->instance(GatewayService::class, $mockGateway);

        $received = $this->trialService()->authorize($sale->services->first(), $this->creditCard());
        $this->assertInstanceOf(Service::class, $received);
    }

    /** @test */
    public function should_save_correct_token_in_authorization(): void
    {
        $service = factory(McAfeeMobileSecurity::class)->make();
        $sale    = (new SaleBuilder())->withServices([$service])->build();

        $this->trialService()->authorize($sale->services->first(), $this->creditCard());

        $this->assertDatabaseHas('sales', [
            'saleTransaction' => $sale->saleTransaction,
            'services.card.token' => Tokenize::TOKEN,
        ], 'mongodb');
    }

    /** @test */
    public function should_save_correct_schedule(): void
    {
        $service    = factory(McAfeeMobileSecurity::class)->make();
        $sale       = (new SaleBuilder())->withServices([$service])->build();
        $creditCard = $this->creditCard();

        $this->trialService()->schedule($sale->services->first(), $creditCard);

        $service = $sale->refresh()->services->first();

        $expiration  = Carbon::instance(data_get($service, 'license.trial.expiration')->toDateTime());
        $statusTrial = data_get($service, 'license.trial.status');

        $this->assertEquals(
            now()->setTimezone('America/Sao_Paulo')->toDateString(),
            $expiration->subDays(30)->setTimezone('America/Sao_Paulo')->toDateString()
        );
        $this->assertEquals(McAfeeStatus::ONGOING, $statusTrial);
    }

    /** @test */
    public function should_return_exception_when_card_unauthorized(): void
    {
        $service = factory(McAfeeMobileSecurity::class)->make();
        $sale    = (new SaleBuilder())->withServices([$service])->build();

        $gatewayServer = \Mockery::mock(Gateway::class)->makePartial();

        $mockConn = \Mockery::mock(GatewayConnection::class)->makePartial();
        $mockConn->shouldReceive('tokenize')->once()->andReturn((new Tokenize())->execute());
        $mockConn->shouldReceive('authorize')->once()->andReturn($gatewayServer);
        $this->instance(GatewayConnection::class, $mockConn);

        try {
            $this->trialService()->authorize($sale->services->first(), $this->creditCard());
        } catch (\Exception $exception) {
            $this->assertEquals(GatewayExceptions::CARD_UNAUTHORIZED, $exception->getShortMessage());
        }

        $this->assertDatabaseHas('sales', [
            'saleTransaction' => $sale->saleTransaction,
            'services.log.card.action' => GatewayMethodsEnum::AUTHORIZE,
            'services.log.card.status' => GatewayStatus::UNAUTHORIZED,
            'services.log.card.message' => trans('gateway::exceptions.' . GatewayExceptions::CARD_UNAUTHORIZED),
        ], 'mongodb');
    }

    private function trialService(): McAfeeTrialService
    {
        return resolve(McAfeeTrialService::class);
    }

    private function creditCard(): array
    {
        return [
            'name' => 'Baby Sharkinho',
            'flag' => 'mastercard',
            'pan' => '5497103665499308',
            'month' => '03',
            'year' => Carbon::now()->format('y'),
            'cvv' => '574'
        ];
    }
}
