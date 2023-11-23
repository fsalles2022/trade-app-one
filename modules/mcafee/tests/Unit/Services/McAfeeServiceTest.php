<?php

namespace McAfee\Tests\Unit\Services;

use Carbon\Carbon;
use Gateway\Services\GatewayService;
use Illuminate\Support\Collection;
use McAfee\Adapters\Response\McAfeeCancelSubscriptionResponseAdapter;
use McAfee\Adapters\Response\McAfeeDisconnectDevicesResponseAdapter;
use McAfee\Adapters\Response\McAfeeNewSubscriptionResponseAdapter;
use McAfee\Connection\McAfeeConnection;
use McAfee\Enumerators\McAfeeActions;
use McAfee\Enumerators\McAfeeStatusCode;
use McAfee\Exceptions\McAfeeExceptions;
use McAfee\Models\McAfeeMobileSecurity;
use McAfee\Services\McAfeeCancelService;
use McAfee\Services\McAfeeService;
use McAfee\Tests\Helpers\McAfeeFactoriesHelper;
use McAfee\Tests\ServerTest\McAfeeServerMock;
use Mockery;
use TradeAppOne\Domain\Components\Helpers\XMLHelper;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\Cancel\CancelServicesService;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class McAfeeServiceTest extends TestCase
{
    use McAfeeFactoriesHelper, ArrayAssertTrait;
    private $mcAfeeService;
    private $service;
    private $user;

    /** @test */
    public function should_return_an_instance_of_mcafee_service(): void
    {
        $this->assertInstanceOf(McAfeeService::class, $this->mcAfeeService);
    }

    /** @test */
    public function should_return_a_collection_when_called_plans(): void
    {
        $network = factory(Network::class)->create(['slug' => 'tradeup-group']);
        $user    = (new UserBuilder())->withNetwork($network)->build();
        $plans   = $this->mcAfeeService->plans($user);
        $this->assertInstanceOf(Collection::class, $plans);
    }

    /** @test */
    public function should_return_correct_structure_when_called_new_subscription(): void
    {
        $gatewayTransactionId = '25656057-A5D1-73A1-0CE9-3B75C61974B4';
        $mcAfeeAdapter        = $this->mcAfeeService->newSubscription($this->service, $gatewayTransactionId);
        $this->assertArrayStructure($mcAfeeAdapter, [
            '@attributes' => [
                'PARTNERREF',
                'REF'
            ],
            'ITEMS'       => [
                'ITEM' => [
                    '@attributes' => [
                        'SKU',
                        'EXPDT'
                    ],
                    'PRODUCTKEY',
                    'PHONE'       => [
                        '@attributes' => [
                            'ACTIVATIONCODE'
                        ]
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function should_return_mcafee_unavailable_exception_when_mcafee_unavailable(): void
    {
        $response         = (new McAfeeServerMock(McAfeeStatusCode::TRANSACTION_FAILED))->ProcessRequestWSResult;
        $responseToArray  = XMLHelper::convertToArray($response);
        $mcAfeeAdapter    = new McAfeeNewSubscriptionResponseAdapter($responseToArray);
        $mcAfeeConnection = Mockery::mock(McAfeeConnection::class)->makePartial();
        $mcAfeeConnection
            ->shouldReceive('newSubscription')
            ->andReturn($mcAfeeAdapter);
        $gatewayService = resolve(GatewayService::class);
        $saleService    = resolve(SaleRepository::class);

        $mcAfeeService        = new McAfeeService($mcAfeeConnection, $gatewayService, $saleService);
        $gatewayTransactionId = '25656057-A5D1-73A1-0CE9-3B75C61974B4';

        $this->expectExceptionMessage(trans(
            'mcAfee::exceptions.' . McAfeeExceptions::MCAFEE_ERROR_ACTIVATING_THE_SALE,
            ['code' => $mcAfeeAdapter->getCode()]
        ));
        $mcAfeeService->newSubscription($this->service, $gatewayTransactionId);
    }

    /** @test */
    public function should_return_array_when_call_cancel_subscription(): void
    {
        $mcAfeeAdapter = $this->mcAfeeService->cancelSubscription($this->service, $this->user);
        $this->assertInternalType('array', $mcAfeeAdapter);
    }

    /** @test */
    public function should_return_exception_when_an_error_occurs_in_the_cancellation(): void
    {
        $mcAfeeConnection = Mockery::mock(McAfeeConnection::class)->makePartial();
        $mcAfeeConnection
            ->shouldReceive('cancelSubscription')
            ->andReturn(new McAfeeCancelSubscriptionResponseAdapter([]));
        $gatewayService = resolve(GatewayService::class);
        $saleRepository = resolve(SaleRepository::class);

        $mcAfeeService = new McAfeeService($mcAfeeConnection, $gatewayService, $saleRepository);

        $this->expectExceptionMessage(trans(
            'mcAfee::exceptions.' . McAfeeExceptions::MCAFEE_ERROR_CANCELING_SUBSCRIPTION
        ));
        $mcAfeeService->cancelSubscription($this->service, $this->user);
    }

    /** @test */
    public function should_return_array_when_call_disconnect_devices(): void
    {
        $mcAfeeAdapter = $this->mcAfeeService->disconnectDevices($this->service, $this->user);
        $this->assertInternalType('array', $mcAfeeAdapter);
    }

    /** @test */
    public function should_return_exception_error_disconnect_devices(): void
    {
        $mcAfeeConnection = Mockery::mock(McAfeeConnection::class)->makePartial();
        $mcAfeeConnection
            ->shouldReceive('disconnectDevices')
            ->andReturn(new McAfeeDisconnectDevicesResponseAdapter([]));
        $gatewayService = resolve(GatewayService::class);
        $saleRepository = resolve(SaleRepository::class);

        $mcAfeeService = new McAfeeService($mcAfeeConnection, $gatewayService, $saleRepository);

        $this->expectExceptionMessage(trans(
            'mcAfee::exceptions.' . McAfeeExceptions::MCAFEE_ERROR_DISCONNECTING_DEVICES
        ));
        $mcAfeeService->disconnectDevices($this->service, $this->user);
    }

    /** @test */
    public function should_return_correct_messsage_when_call_refund_subscription_with_correct_parameters(): void
    {
        $service = $this->mcAfeeFactories()->of(McAfeeMobileSecurity::class)->make([
            'status' => ServiceStatus::APPROVED
        ]);
        (new SaleBuilder())->withServices([$service])->build();

        $response = $this->cancelService()->cancel($this->user, $service->serviceTransaction);
        $this->assertEquals(
            trans('mcAfee::messages.subscription.canceled', ['label' => $this->service->label]),
            $response
        );
    }

    /** @test */
    public function should_persist_data_when_call_refund_subscription_wit_correct_parameters(): void
    {
        $service = $this->mcAfeeFactories()->of(McAfeeMobileSecurity::class)->make([
            'status' => ServiceStatus::APPROVED
        ]);
        (new SaleBuilder())->withServices([$service])->build();

        $this->cancelService()->cancel($this->user, $service->serviceTransaction);

        $this->assertDatabaseHas(
            'sales',
            [
                'services.serviceTransaction' => $service->serviceTransaction,
                'services.status'             => ServiceStatus::CANCELED,
                'services.log.name'           => $this->user->firstName . " " . $this->user->lastName,
                'services.log.cpf'            => $this->user->cpf,
                'services.log.action'         => 'CANCELLATION',
                'services.log.message'        => trans('mcAfee::messages.subscription.canceled', ['label' => $service->label]),

            ],
            'mongodb'
        );
    }

    /** @test */
    public function should_return_exception_when_service_is_not_approved_to_cancel(): void
    {
        $this->expectExceptionMessage(trans('exceptions.service.' . ServiceExceptions::ACTIVE_TO_CANCEL));
        $this->cancelService()->cancel($this->user, $this->service->serviceTransaction);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $service = $this->mcAfeeFactories()->of(McAfeeMobileSecurity::class)->make();
        $sale    = (new SaleBuilder())->withServices([$service])->build();

        $this->mcAfeeService = resolve(McAfeeService::class);
        $this->service       = $sale->services->first();
        $this->user          = (new UserBuilder())->build();
    }

    private function cancelService(): CancelServicesService
    {
        return resolve(CancelServicesService::class);
    }
}
