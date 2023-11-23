<?php

namespace Reports\Services;

use Reports\Adapters\QueryResults\TotalSalesPerStatusAdapter;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class TotalSalesPerStatusService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getTotalSalesPerStatus(array $filters)
    {
        $query = $this->getQuery();
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::PERFORMED_SALES);
        }

        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);

        $collection = $this->saleReportRepository->getFilteredByContext($filteredQuery);

        return TotalSalesPerStatusAdapter::adapt($collection);
    }

    private function getQuery()
    {
        $aggsSumPrices = (new ElasticsearchAggregationStructure('status_count'))
            ->terms('service_status.keyword');

        return (new ElasticsearchQueryBuilder)
            ->where('service_sector', Operations::TELECOMMUNICATION)
            ->size(0)
            ->aggregations($aggsSumPrices)
            ->get();
    }
}
