<?php


namespace Reports\Tests\Unit\Services;

use Reports\Tests\Fixture\ElasticSearchMonthGroupOfSales;
use Reports\Services\SalesByGroupOfOperationsService;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\TestCase;

class SalesByGroupOfOperationsServiceTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;

    protected $endpoint = 'reports/number/group-of-telecommunication-operations';

    /** @test */
    public function should_return_with_zero_on_prepagos_when_operator_has_not_prepago()
    {
        $elasticConnetion = \Mockery::mock(SaleReportRepository::class)->makePartial();
        $elasticConnetion->shouldReceive('getFilteredByContext')->andReturn(collect(ElasticSearchMonthGroupOfSales::fixture()));

        $service                   = new SalesByGroupOfOperationsService($elasticConnetion);
        $result                    = $service->getSalesByGroupOfOperations([]);
        $operatorsCollectionResult = collect($result['operators']);
        $oiResume                  = $operatorsCollectionResult->where('operator', Operations::OI)->first();
        $timResume                 = $operatorsCollectionResult->where('operator', Operations::TIM)->first();

        self::assertEquals($oiResume['prePago']['quantity'], 0);
        self::assertEquals($timResume['prePago']['quantity'], 0);
    }

    /** @test */
    public function should_not_return_operator_is_not_telecommunication()
    {
        $elasticConnetion = \Mockery::mock(SaleReportRepository::class)->makePartial();
        $elasticConnetion->shouldReceive('getFilteredByContext')->andReturn(collect(ElasticSearchMonthGroupOfSales::fixture()));

        $service                       = new SalesByGroupOfOperationsService($elasticConnetion);
        $result                        = $service->getSalesByGroupOfOperations([]);
        $operatorsCollectionResult     = collect($result['operators']);
        $operatorsNotTelecommunication = $operatorsCollectionResult->whereNotIn(
            'operator',
            array_keys(Operations::TELECOMMUNICATION_OPERATORS)
        );
        self::assertEmpty($operatorsNotTelecommunication);
    }
}
