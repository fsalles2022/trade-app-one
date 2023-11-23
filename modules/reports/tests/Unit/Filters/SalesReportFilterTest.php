<?php

namespace Reports\Tests\Unit\Filters;

use Carbon\Carbon;
use TradeAppOne\Domain\Repositories\Filters\SalesReportFilter;
use TradeAppOne\Tests\TestCase;

class SalesReportFilterTest extends TestCase
{
    /** @test */
    public function should_apply_filter_by_serviceTransaction()
    {
        $filters = ['serviceTransaction' => '123'];

        $this->assertEquals('service_servicetransaction.keyword:123', $this->apply($filters));
    }

    /** @test */
    public function should_apply_filter_by_startDate()
    {
        $date         = '2019-08-30';
        $expectedDate = Carbon::parse($date)->toIso8601String();
        $filters      = ['startDate' => $date];

        $this->assertEquals("created_at:[$expectedDate TO *]", $this->apply($filters));
    }

    /** @test */
    public function should_apply_filter_by_endDate()
    {
        $date         = '2019-08-30';
        $expectedDate = Carbon::parse($date)->toIso8601String();
        $filters      = ['endDate' => $date];

        $this->assertEquals("created_at:[* TO $expectedDate]", $this->apply($filters));
    }

    /** @test */
    public function should_apply_filter_by_cpfSalesman()
    {
        $cpf     = '12345678';
        $filters = ['cpfSalesman' => $cpf];

        $this->assertEquals("user_cpf.keyword:$cpf", $this->apply($filters));
    }

    /** @test */
    public function should_apply_filter_by_pointOfSaleCnpj()
    {
        $filters = ['pointOfSaleCnpj' => ['123', '456']];

        $this->assertEquals("pointofsale_cnpj.keyword:(123 OR 456)", $this->apply($filters));
    }

    /** @test */
    public function should_apply_filter_by_pointOfSaleSlug()
    {
        $filters = ['pointOfSaleSlug' => ['abc', 'def']];

        $this->assertEquals("pointofsale_slug.keyword:(abc OR def)", $this->apply($filters));
    }

    private function apply(array $filters): string
    {
        return (new SalesReportFilter())->apply($filters)->getQuery()->toStringQuery();
    }
}
