<?php

namespace Reports\Tests\Helpers;

use Reports\Helpers\ReportDateHelper;
use TradeAppOne\Tests\TestCase;

class ReportDataHelperTest extends TestCase
{
    /** @test */
    public function should_return_date_when_start_and_end_date_not_exist()
    {
        $assert = [
            'startDate' => now()->startOfMonth()->format('d/m/y'),
            'endDate'   => now()->format('d/m/y')
        ];

        $received = ReportDateHelper::periodWithCriteriaMonthly([]);
        $this->assertArraySubset($assert, $received);
    }

    /** @test */
    public function should_return_date_when_only_start_date_exist()
    {
        $filters = [
            'startDate' => '2019-05-01'
        ];

        $received = ReportDateHelper::periodWithCriteriaMonthly($filters);

        $assert = [
            'startDate' => '01/05/19',
            'endDate'   => now()->format('d/m/y')
        ];

        $this->assertArraySubset($assert, $received);
    }

    /** @test */
    public function should_return_date_when_only_end_date_exist()
    {
        $filters = [
            'endDate' => '2019-06-01'
        ];

        $received = ReportDateHelper::periodWithCriteriaMonthly($filters);

        $assert = [
            'startDate' => '',
            'endDate'   => '01/06/19'
        ];

        $this->assertArraySubset($assert, $received);
    }

    /** @test */
    public function should_return_startDate_of_endDate_when_defined_startDateMonthly()
    {
        $filters = [
            'endDate' => '2019-05-14'
        ];

        $received = ReportDateHelper::periodWithCriteriaMonthly($filters, true);

        $assert = [
            'startDate' => '01/05/19',
            'endDate'   => '14/05/19'
        ];

        $this->assertArraySubset($assert, $received);
    }
}
