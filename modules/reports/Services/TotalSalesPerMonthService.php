<?php

namespace Reports\Services;

use Carbon\Carbon;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class TotalSalesPerMonthService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getTotalSalesPerMonth(array $filters)
    {
        $query = $this->getQuery();
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);

        $collection = $this->saleReportRepository->getFilteredByContext($filteredQuery);

        $elasticSearchArray = $collection->toArray();

        $operators = data_get($elasticSearchArray, 'aggregations.operator.buckets');

        return [
            'operators' => $operators,
            'total' => data_get($elasticSearchArray, 'hits.total', 0),
            'totalPrice' => data_get($elasticSearchArray, 'aggregations.sum_price.value', 0)
        ];
    }

    private function getQuery()
    {
        $beginningOfMonthDateString = (Carbon::now())->startOfMonth()->toIso8601String();
        $nowDateString              = (Carbon::now())->toIso8601String();

        $aggsOperatorPrices = (new ElasticsearchAggregationStructure('sum_price'))
            ->sum('service_price');

        $aggsOperator = (new ElasticsearchAggregationStructure('operator'))
            ->terms('service_operator.keyword')
            ->nest($aggsOperatorPrices);

        $aggsTotalPrice = (new ElasticsearchAggregationStructure('sum_price'))
            ->sum('service_price')
            ->brother($aggsOperator);

        return (new ElasticsearchQueryBuilder)
            ->where('created_at', "[{$beginningOfMonthDateString} TO {$nowDateString}]")
            ->size(0)
            ->aggregations($aggsTotalPrice)
            ->get();
    }
}
