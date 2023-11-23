<?php

namespace Reports\Services;

use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class TotalSalesPerOperatorService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getTotalSalesPerOperator(array $filters)
    {
        $query = $this->getQuery();
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);

        $collection = $this->saleReportRepository->getFilteredByContext($filteredQuery);

        $elasticSearchArray = $collection->toArray();
        $buckets            = data_get($elasticSearchArray, 'aggregations.operator.buckets');

        $operators       = data_get($elasticSearchArray, 'aggregations.operator.buckets');
        $totalSalesPrice = [];

        return [
            'operators' => $operators,
            'total' => data_get($elasticSearchArray, 'hits.total', 0)
        ];
    }

    private function getQuery()
    {
        $aggsOperationPrices = (new ElasticsearchAggregationStructure('sum_price'))
            ->sum('service_price');
        $aggsOperation       = (new ElasticsearchAggregationStructure('operation'))
            ->terms('service_operation.keyword')
            ->sumToSameParentLevel('service_price')
            ->nest($aggsOperationPrices);
        $aggsOperator        = (new ElasticsearchAggregationStructure('operator'))
            ->terms('service_operator.keyword')
            ->nest($aggsOperation);

        return (new ElasticsearchQueryBuilder)
          ->size(0)
          ->aggregations($aggsOperator)
          ->get();
    }
}
