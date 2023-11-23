<?php

namespace Reports\Tests\Unit\Services;

use Carbon\Carbon;
use Reports\Tests\Fixture\ElasticSearchSalesByMonthAndPeriodFixture;
use Reports\Services\SalesByMonthAndPeriod;
use Reports\SubModules\Hourly\Helpers\CriteriaHourlyDminus;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;
use TradeAppOne\Tests\TestCase;

class SalesByMonthAndPeriodTest extends TestCase
{
    use ElasticSearchHelper;

    /** @test */
    public function should_return_an_instance()
    {
        $elasticFixture = ElasticSearchSalesByMonthAndPeriodFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);

        $class = new SalesByMonthAndPeriod(resolve(SaleReportRepository::class));
        $this->assertInstanceOf(SalesByMonthAndPeriod::class, $class);
    }

    /** @test */
    public function should_return_an_array_with_sale_structure()
    {
        $elasticFixture = ElasticSearchSalesByMonthAndPeriodFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $strategyCriteria = new CriteriaHourlyDminus(Carbon::now());

        $class  = new SalesByMonthAndPeriod(resolve(SaleReportRepository::class));
        $result = $class->getResume([], $strategyCriteria);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }
}
