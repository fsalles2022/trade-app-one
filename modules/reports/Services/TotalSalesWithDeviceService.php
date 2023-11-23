<?php

namespace Reports\Services;

use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class TotalSalesWithDeviceService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getTotalSalesWithDevice(array $filters)
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
            'total' => data_get($elasticSearchArray, 'hits.total'),
            'operators'  => $operators
        ];
    }

    private function getQuery()
    {
        $aggsPriceOperation = (new ElasticsearchAggregationStructure('sum_price'))
            ->sum('service_price');

        $aggsOperation = (new ElasticsearchAggregationStructure('operation'))
            ->terms('service_operation.keyword')
            ->sumToSameParentLevel('service_price')
            ->nest($aggsPriceOperation);

        $aggsOperator = (new ElasticsearchAggregationStructure('operator'))
            ->terms('service_operator.keyword')
            ->nest($aggsOperation);


        return (new ElasticsearchQueryBuilder)
            ->whereIn('service_operator', array_keys(Operations::TELECOMMUNICATION_OPERATORS))
            ->exists('service_imei.keyword')
            ->size(0)
            ->aggregations($aggsOperator)
            ->get();
    }
}
