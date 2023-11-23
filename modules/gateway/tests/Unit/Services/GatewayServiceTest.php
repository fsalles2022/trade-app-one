<?php

namespace TradeAppOne\Tests\Feature;

use Gateway\API\Gateway;
use Gateway\Components\Interest;
use Gateway\Connection\GatewayConnection;
use Gateway\Enumerators\GatewayStatus;
use Gateway\Exceptions\GatewayExceptions;
use Gateway\Services\GatewayService;
use Gateway\tests\Helpers\GatewayFactoriesHelper;
use Gateway\tests\ServerTest\Methods\Sale;
use McAfee\Models\McAfeeMobileSecurity;
use McAfee\Tests\Helpers\McAfeeFactoriesHelper;
use Mockery;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;
use TradeAppOne\Facades\Uniqid;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class GatewayServiceTest extends TestCase
{
    use ArrayAssertTrait, GatewayFactoriesHelper, McAfeeFactoriesHelper;

    private $service;

    protected function setUp()
    {
        parent::setUp();
        $service       = $this->mcAfeeFactories()->of(McAfeeMobileSecurity::class)->make();
        $sale          = (new SaleBuilder())->withServices([$service])->build();
        $this->service = $this->gatewayService()->tokenize($sale->services()->first(), $this->payloadCreditCard());
    }

    /** @test */
    public function should_return_an_instance_of_gateway()
    {
        $response = $this->gatewayService()->sale($this->service);
        $this->assertInstanceOf(Gateway::class, $response);
    }

    /** @test */
    public function should_persist_gateway_reference()
    {
        $uniqid = Uniqid::generate();
        Uniqid::shouldReceive('generate')->andReturn($uniqid);
        $response = $this->gatewayService()->sale($this->service);
        $assert   = [
            'services.payment.gatewayReference' => $uniqid,
            'services.payment.gatewayTransactionId' => $response->getTransactionID(),
            'services.payment.gatewayStatus' => GatewayStatus::AUTHORIZED,
            'services.payment.status' => ServiceStatus::APPROVED,
            'services.payment.date' => data_get($this->service->toArray(), 'payment.date'),
            'services.payment.times' => 1,
            'services.payment.interest' => 0.0,
        ];
        $this->assertDatabaseHas('sales', $assert, 'mongodb');
    }

    /** @test */
    public function should_return_exception_when_transaction_not_approved()
    {
        $gateway = Mockery::mock(GatewayConnection::class)->makePartial();
        $gateway->shouldReceive('sale')->andReturn((new Sale())->execute(9999));//9999 is UNKNOWN
        $this->instance(GatewayConnection::class, $gateway);

        $this->expectExceptionMessage(trans('gateway::exceptions.' . GatewayExceptions::GATEWAY_TRANSACTION_NOT_APPROVED));

        $this->gatewayService()->sale($this->service);
    }

    /** @test */
    public function should_return_response_with_a_correct_structure()
    {
        $response = $this->gatewayService()->sale($this->service);

        $this->assertArrayStructure($response->getResponse(), [
            'transactionId',
            'operationId',
            'status',
            'message',
            'log',
            'errorCode',
            'order' => [
                'reference',
                'currency',
                'totalAmount',
                'dateTime'
            ],
            'processor' => [
                'acquirer',
                'acquirerId',
                'tid',
                'paymentId',
                'type',
                'amount',
                'brand',
                'numberOfPayments',
                'interest',
                'currency',
                'authorizationCode',
                'urlAuthentication',
                'serviceTaxAmount',
                'returnCode',
                'returnMessage',
                'proofOfSale',
                'provider',
                'status',
                'receivedDate',
                'softDescriptor',
                'capture',
                'tokenCard'
            ]
        ]);
    }

    /** @test */
    public function should_return_exception_when_transaction_id_not_found()
    {
        $service = factory(McAfeeMobileSecurity::class)->make(['payment' => []]);

        $this->expectExceptionCode(GatewayExceptions::TRANSACTION_ID_NOT_FOUND);
        $this->gatewayService()->getTransactionId($service);
    }

    /** @test */
    public function should_persist_payment_status_when_cancel_with_success()
    {
        $this->service->payment = ['gatewayTransactionId' => '1234'];

        $this->gatewayService()->cancel($this->service);

        $this->assertDatabaseHas('sales', [
            'services.payment.status' => ServiceStatus::CANCELED
        ], 'mongodb');
    }

    /** @test */
    public function should_return_exception_when_it_fails_to_cancel_the_sale()
    {
        $gateway = Mockery::mock(GatewayConnection::class)->makePartial();
        $gateway->shouldReceive('cancel')->andReturn((new Sale())->execute(9)); //9 is UNAUTHORIZED

        $this->instance(GatewayConnection::class, $gateway);

        $this->expectExceptionMessage(
            trans('gateway::exceptions.' . GatewayExceptions::GATEWAY_ERROR_CANCELING_THE_SALE)
        );

        $service = factory(McAfeeMobileSecurity::class)->make(['payment' => ['gatewayTransactionId' => '1234']]);
        $this->gatewayService()->cancel($service);
    }

    /** @test */
    public function should_return_exception_when_service_not_contains_token_card()
    {
        $service = factory(McAfeeMobileSecurity::class)->make();

        $this->expectExceptionCode(ServiceExceptions::TOKEN_CARD_NOT_FOUND);
        $this->gatewayService()->getTokenCard($service);
    }

    /** @test */
    public function should_return_exception_when_unauthorized()
    {
        $gateway = Mockery::mock(GatewayConnection::class)->makePartial();
        $gateway->shouldReceive('authorize')->andReturn((new Sale())->execute(9));//9 is UNAUTHORIZED

        $this->instance(GatewayConnection::class, $gateway);

        $service = factory(McAfeeMobileSecurity::class)->make([
            'payment' => ['gatewayTransactionId' => '1234'],
            'card' => [
                'token' => '1234324234'
            ]
        ]);

        $this->expectExceptionCode(GatewayExceptions::CARD_UNAUTHORIZED);
        $this->gatewayService()->authorize($service);
    }

    /** @test */
    public function should_return_cancel_transaction_in_authorize_when_is_authorized()
    {
        $gateway = Mockery::mock(GatewayConnection::class)->makePartial();
        $gateway->shouldReceive('authorize')->andReturn((new Sale())->execute());//9 is UNAUTHORIZED
        $gateway->shouldReceive('cancel')->andReturn((new Sale())->execute());

        $this->instance(GatewayConnection::class, $gateway);

        $service = factory(McAfeeMobileSecurity::class)->make([
            'payment' => ['gatewayTransactionId' => '1234'],
            'card' => [
                'token' => '1234324234'
            ]
        ]);

        //$this->expectExceptionCode(GatewayExceptions::CARD_UNAUTHORIZED);
        $received = $this->gatewayService()->authorize($service);
        $this->assertInstanceOf(Gateway::class, $received);
    }

    /** @test */
    public function should_apply_correct_interest_when_withInterest_is_true()
    {
        for ($i = 0; $i <= 10; $i++) {
            $priceExpected = self::rand_float();

            $service = factory(Service::class)->make([
                'price' => Interest::apply($priceExpected, 1),
                'customer' => [
                    'cpf' => '00000009652',
                    'name' => 'Pecheche Lima',
                    'email' => 'pecheche@mail.com',
                ],
                'card' => [
                    'token' => '4370f54683c461a182d9914d9d7581bb0308cbf404b8bf473d298febd1bb4d96',
                ]
            ]);

            $service->operator = substr($service->operator, 0, 12);
            $this->gatewayService()->sale($service, random_int(1, 12), true);

            $interest = $service->payment['interest'];
            $price    = $service->price - $interest;

            $this->assertEquals($priceExpected, $price);
        }
    }

    /** @test */
    public function should_not_apply_interest_when_withInterest_is_false()
    {
        $priceExpected = self::rand_float();

        $service           = factory(Service::class)->make([
            'price' => $priceExpected,
            'customer' => [
                'cpf' => '00000009652',
                'name' => 'Pecheche Lima',
                'email' => 'pecheche@mail.com',
            ],
            'card' => [
                'token' => '4370f54683c461a182d9914d9d7581bb0308cbf404b8bf473d298febd1bb4d96',
            ]
        ]);
        $service->operator = substr($service->operator, 0, 12);
        $this->gatewayService()->sale($service, random_int(1, 12));

        $interest = $service->payment['interest'];

        $this->assertEquals(0.0, $interest);
        $this->assertEquals($priceExpected, $service->price);
    }

    private static function rand_float($st_num = 400, $end_num = 5000, $mul = 1000000)
    {
        $num = random_int($st_num * $mul, $end_num * $mul)/$mul;

        return round($num, 2);
    }

    private function gatewayService(): GatewayService
    {
        return resolve(GatewayService::class);
    }
}
