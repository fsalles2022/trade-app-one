<?php

namespace McAfee\Tests\Unit\Console;

use Carbon\Carbon;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use McAfee\Adapters\Response\McAfeeCancelSubscriptionResponseAdapter;
use McAfee\Connection\McAfeeConnection;
use McAfee\Console\McAfeeTrialCommand;
use McAfee\Enumerators\McAfeeStatus;
use McAfee\Exceptions\McAfeeExceptions;
use McAfee\Models\McAfeeMobileSecurity;
use TradeAppOne\Domain\Components\Helpers\MongoDateHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class McAfeeTrialCommandTest extends TestCase
{
    public const COMMAND = 'mcAfee:trial';

    /** @test */
    public function should_be_registered_command(): void
    {
        $this->assertTrue(array_has(Artisan::all(), self::COMMAND));
    }

    /** @test */
    public function should_charge_and_update_status_trial(): void
    {
        $sale = (new SaleBuilder())->withServices([$this->correctService()])->build();

        Artisan::call(self::COMMAND);

        $service        = $sale->refresh()->services->first();
        $startDate      = Carbon::instance(data_get($service->license, 'start')->toDateTime());
        $expirationDate = Carbon::instance(data_get($service->license, 'expiration')->toDateTime());
        $status         = data_get($service->license, 'trial.status');

        $this->assertEquals(0, $startDate->diffInDays());
        $this->assertEquals(365, $expirationDate->diffInDays($startDate, true));
        $this->assertEquals(McAfeeStatus::FINISHED, $status);

        $this->assertEquals(ServiceStatus::APPROVED, data_get($service->payment, 'status'));
        $this->assertTrue(array_has($service->payment, ['gatewayReference', 'gatewayTransactionId', 'gatewayStatus', 'date']));
    }
    
    /** @test */
    public function should_reject_and_update_trial_when_error_occurs(): void
    {
        //no TokenCard
        $service = factory(McAfeeMobileSecurity::class)->make([
            'serviceTransaction' => '123',
            'status' => ServiceStatus::APPROVED,
            'operation' => Operations::MCAFEE_MULTI_ACCESS_TRIAL,
            'retryPayment' => true,
            'license' => [
                'trial' => [
                    'status' => McAfeeStatus::ONGOING,
                    'expiration' => MongoDateHelper::dateTimeToUtc(now()->subDay())
                ],
                'quantity' => '1'
            ],
            'payment' => [
                'times' => 3
            ]
        ]);

        (new SaleBuilder())->withServices([$service])->build();

        Artisan::call(self::COMMAND);

        $this->assertDatabaseHas('sales', [
            'services.status' => ServiceStatus::CANCELED,
            'services.license.trial.status' => McAfeeStatus::REJECTED,
            'services.statusThirdParty' => ServiceStatus::CANCELED,
            'services.log.action' => McAfeeTrialCommand::ACTION,
            'services.log.info' =>  trans('exceptions.service.'. ServiceExceptions::TOKEN_CARD_NOT_FOUND),
            'services.log.message' => trans('mcAfee::messages.error_billing_trial'),
            'services.log.status' => McAfeeTrialCommand::ERROR_PAYMENT
        ], 'mongodb');
    }

    /** @test */
    public function should_log_critical_and_update_trial_when_error_occurs_in_cancel_subscription(): void
    {
        //Error no TokenCard
        $service = factory(McAfeeMobileSecurity::class)->make([
            'serviceTransaction' => '123',
            'status' => ServiceStatus::APPROVED,
            'operation' => Operations::MCAFEE_MULTI_ACCESS_TRIAL,
            'retryPayment' => true,
            'license' => [
                'trial' => [
                    'status' => McAfeeStatus::ONGOING,
                    'expiration' => MongoDateHelper::dateTimeToUtc(now()->subDay())
                ],
                'quantity' => '1',
            ],
            'payment' => [
                'times' => 3,
            ]
        ]);

        (new SaleBuilder())->withServices([$service])->build();

        //Error no Success
        $mockConn     = \Mockery::mock(McAfeeConnection::class)->makePartial();
        $mockResponse = \Mockery::mock(McAfeeCancelSubscriptionResponseAdapter::class)->makePartial();
        $mockResponse->shouldReceive('isSuccess')->once()->andReturnFalse();
        $mockConn->shouldReceive('cancelSubscription')->once()->andReturn($mockResponse);

        $this->app->instance(McAfeeConnection::class, $mockConn);

        $mockLogger = \Mockery::mock(Logger::class)->makePartial();
        $mockLogger->shouldReceive('critical')->once();
        Log::shouldReceive('channel')->once()->andReturn($mockLogger);

        Artisan::call(self::COMMAND);

        $this->assertDatabaseHas('sales', [
            'services.log.action'  => McAfeeTrialCommand::ACTION,
            'services.log.info' => trans('mcAfee::exceptions.'. McAfeeExceptions::MCAFEE_ERROR_CANCELING_SUBSCRIPTION),
            'services.log.message'    => trans('mcAfee::messages.trial_cancel_error'),
            'services.log.status'  => McAfeeTrialCommand::ERROR_PAYMENT_AND_CANCELING
        ], 'mongodb');
    }

    private function correctService()
    {
        return factory(McAfeeMobileSecurity::class)->make([
            'serviceTransaction' => '123',
            'status' => ServiceStatus::APPROVED,
            'operation' => Operations::MCAFEE_MULTI_ACCESS_TRIAL,
            'card' => [
                'token' => '123'
            ],
            'license' => [
                'trial' => [
                    'status' => McAfeeStatus::ONGOING,
                    'expiration' => MongoDateHelper::dateTimeToUtc(now()->subDay())
                ],
                'quantity' => '1'
            ]
        ]);
    }
}
