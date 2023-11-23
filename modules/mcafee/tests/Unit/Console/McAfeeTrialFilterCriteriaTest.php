<?php

namespace McAfee\Tests\Unit\Console;

use McAfee\Console\McAfeeTrialCommand;
use McAfee\Enumerators\McAfeeStatus;
use McAfee\Models\McAfeeMobileSecurity;
use TradeAppOne\Domain\Components\Helpers\MongoDateHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class McAfeeTrialFilterCriteriaTest extends TestCase
{
    /** @test */
    public function should_return_correct_service_findService()
    {
        $sale = (new SaleBuilder())->withServices([$this->correctService()])->build();

        $command  = new McAfeeTrialCommand();
        $services = $command->findServices();

        $this->assertCount(1, $services);
        $this->assertEquals($sale->services->first()->id, $services->first()->id);
    }

    /** @test */
    public function should_return_service_with_correct_filter_criteria()
    {
        $services = collect();
        $services->push($this->correctService());

        $command  = new McAfeeTrialCommand();
        $filtered = $command->filterCriteria($services, '');

        $this->assertCount(1, $filtered);
    }

    /** @test */
    public function should_return_correct_service_when_status_is_not_approved()
    {
        $services = collect();
        $services->push($this->correctService());

        $services->push(factory(McAfeeMobileSecurity::class)->make([
            'status' => ServiceStatus::REJECTED,
            'operation' => Operations::MCAFEE_MULTI_ACCESS_TRIAL,
            'license' => [
                'trial' => [
                    'status' => McAfeeStatus::ONGOING,
                    'expiration' => MongoDateHelper::dateTimeToUtc(now())
                ]
            ]
        ]));

        $command  = new McAfeeTrialCommand();
        $filtered = $command->filterCriteria($services, '');

        $this->assertCount(1, $filtered);
        $this->assertEquals('123', $filtered->first()->serviceTransaction);
    }

    /** @test */
    public function should_return_correct_service_when_operation_is_not_trial()
    {
        $services = collect();
        $services->push($this->correctService());

        $services->push(factory(McAfeeMobileSecurity::class)->make([
            'status' => ServiceStatus::APPROVED,
            'operation' => Operations::MCAFEE_MOBILE_SECURITY,
            'license' => [
                'trial' => [
                    'status' => McAfeeStatus::ONGOING,
                    'expiration' => MongoDateHelper::dateTimeToUtc(now())
                ]
            ]
        ]));

        $command  = new McAfeeTrialCommand();
        $filtered = $command->filterCriteria($services, '');

        $this->assertCount(1, $filtered);
        $this->assertEquals('123', $filtered->first()->serviceTransaction);
    }

    /** @test */
    public function should_return_correct_service_when_trial_status_is_not_ongoing()
    {
        $services = collect();
        $services->push($this->correctService()); //today

        $services->push(factory(McAfeeMobileSecurity::class)->make([
            'status' => ServiceStatus::APPROVED,
            'operation' => Operations::MCAFEE_MULTI_ACCESS_TRIAL,
            'license' => [
                'trial' => [
                    'status' => McAfeeStatus::FINISHED,
                    'expiration' => MongoDateHelper::dateTimeToUtc(now())
                ]
            ]
        ]));

        $command  = new McAfeeTrialCommand();
        $filtered = $command->filterCriteria($services, '');

        $this->assertCount(1, $filtered);
        $this->assertEquals('123', $filtered->first()->serviceTransaction);
    }

    /** @test */
    public function should_return_correct_service_when_trial_is_not_expired()
    {
        // Expire next day
        $services = collect();
        $services->push($this->correctService());

        $services->push(factory(McAfeeMobileSecurity::class)->make([
            'status' => ServiceStatus::APPROVED,
            'operation' => Operations::MCAFEE_MULTI_ACCESS_TRIAL,
            'license' => [
                'trial' => [
                    'status' => McAfeeStatus::ONGOING,
                    'expiration' => MongoDateHelper::dateTimeToUtc(now()->addDay())
                ]
            ]
        ]));

        // already expiration
        $services->push(factory(McAfeeMobileSecurity::class)->make([
            'status' => ServiceStatus::APPROVED,
            'operation' => Operations::MCAFEE_MULTI_ACCESS_TRIAL,
            'license' => [
                'trial' => [
                    'status' => McAfeeStatus::ONGOING,
                    'expiration' => MongoDateHelper::dateTimeToUtc(now()->subDay())
                ]
            ]
        ]));

        $command  = new McAfeeTrialCommand();
        $filtered = $command->filterCriteria($services, '');

        $this->assertCount(2, $filtered);
    }

    private function correctService()
    {
        return factory(McAfeeMobileSecurity::class)->make([
            'serviceTransaction' => '123',
            'status' => ServiceStatus::APPROVED,
            'operation' => Operations::MCAFEE_MULTI_ACCESS_TRIAL,
            'license' => [
                'trial' => [
                    'status' => McAfeeStatus::ONGOING,
                    'expiration' => MongoDateHelper::dateTimeToUtc(now())
                ]
            ]
        ]);
    }
}
