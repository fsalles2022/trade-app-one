<?php

namespace Reports\Tests\SubModules\Hourly\Helpers;

use Carbon\Carbon;
use Reports\SubModules\Hourly\Helpers\CriteriaHourlyDminus;
use TradeAppOne\Tests\TestCase;

class CriteriaHourlyDminusTest extends TestCase
{
    /** @test */
    public function should_return_period_start_of_D3_strategy_friday()
    {
        $monday            = Carbon::now()->startOfWeek()->hour(7);
        list($start, $end) = CriteriaHourlyDminus::period($monday);
        self::assertEquals($monday->subDays(3)->startOfDay(), $start->startOfDay());
        self::assertTrue($start->isFriday());
    }

    /** @test */
    public function should_return_period_end_of_D3_strategy_yesterday()
    {
        $monday            = Carbon::now()->startOfWeek()->hour(7);
        list($start, $end) = CriteriaHourlyDminus::period($monday);
        self::assertEquals($monday->subDays(1)->endOfDay(), $end);
        self::assertTrue($end->isSunday());
    }


    /** @test */
    public function should_return_period_end_of_D1_with_any_day()
    {
        $anyDay            = Carbon::now()->subWeek()->startOfWeek()->addDays(2);
        list($start, $end) = CriteriaHourlyDminus::period($anyDay);
        self::assertEquals($anyDay->subDays(1)->endOfDay(), $end);
        self::assertFalse($anyDay->isMonday());
    }

    /** @test */
    public function should_return_of_D3_strategy()
    {
        $monday   = Carbon::now()->startOfWeek()->hour(7);
        $strategy = CriteriaHourlyDminus::strategy($monday);
        self::assertEquals(CriteriaHourlyDminus::D3, $strategy);
    }

    /** @test */
    public function should_return_of_D1_strategy()
    {
        $anyDay   = Carbon::now()->subWeek()->startOfWeek()->addDays(2);
        $strategy = CriteriaHourlyDminus::strategy($anyDay);
        self::assertEquals(CriteriaHourlyDminus::D1, $strategy);
    }
}
