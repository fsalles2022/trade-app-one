<?php

namespace Reports\Tests\SubModules\Hourly;

use Carbon\Carbon;
use Reports\Tests\Fixture\ElasticSearchSalesByMonthAndPeriodFixture;
use Reports\Tests\Fixture\HourlyLayoutFixture;
use Reports\Goals\Enum\GoalsTypesEnum;
use Reports\Goals\Services\GoalService;
use Reports\Services\SalesByMonthAndPeriod;
use Reports\SubModules\Hourly\HourlyReportService;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Tests\Helpers\Builders\GoalBuilder;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\TestCase;

class HourlyReportServiceTest extends TestCase
{
    use ElasticSearchHelper;

    /** @test */
    public function should_return_an_instance(): void
    {
        $class = new HourlyReportService(
            resolve(HierarchyService::class),
            resolve(NetworkService::class),
            resolve(GoalService::class),
            resolve(SalesByMonthAndPeriod::class)
        );

        $this->assertInstanceOf(HourlyReportService::class, $class);
    }

    /** @test */
    public function should_get_return_an_array_with_valid_structure(): void
    {
        $network         = (new NetworkBuilder())->withAllServices()->withSlug(NetworkEnum::RIACHUELO)->build();
        $hierarchy       = (new HierarchyBuilder())->withNetwork($network)->withSlug(NetworkEnum::RIACHUELO.'.hierarchy')->build();
        $pointOfSale     = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->withParams([
            'cnpj' => ElasticSearchSalesByMonthAndPeriodFixture::CNPJ_SALES_MONTH_AND_PERIOD,
            'label' => NetworkEnum::RIACHUELO . '.pdv',
        ])->build();
        $dateFromFixture = Carbon::create(date('Y'), 1, 2);
        Carbon::setTestNow($dateFromFixture);

        (new GoalBuilder)->withValue(78)
            ->withPointOfSale($pointOfSale)
            ->withTypeString(GoalsTypesEnum::TOTAL)
            ->withMonth($dateFromFixture->month)
            ->build();

        $elasticFixture = ElasticSearchSalesByMonthAndPeriodFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);

        $class = new HourlyReportService(
            resolve(HierarchyService::class),
            resolve(NetworkService::class),
            resolve(GoalService::class),
            resolve(SalesByMonthAndPeriod::class)
        );

        $result = $class->get(['network' => NetworkEnum::RIACHUELO]);

        $this->assertEquals($result['HEADERS'], HourlyLayoutFixture::getSaleWithPrice()['HEADERS']);
        $this->assertEquals($result['BODY'], HourlyLayoutFixture::getSaleWithPrice()['BODY']);
    }
}
