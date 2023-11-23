<?php

namespace Reports\Services;

use Reports\Adapters\QueryResults\TopPointOfSalesByOperatorAdapter;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class TopPointOfSalesByOperatorService
{
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getTopsPointsOfSalesByOperator(array $filters)
    {
        $query = $this->getQuery();

        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $collection = $this->saleReportRepository->getFilteredByContext($filteredQuery);
        return array_merge(
            ['title' => trans('reports::chartnames.line.top_pointofsales_by_operation', ['size' => 10])],
            TopPointOfSalesByOperatorAdapter::adapt($collection)
        );
    }

    private function getQuery()
    {
        $aggsOperator = (new ElasticsearchAggregationStructure('operators'))
            ->terms('service_operator.keyword', ['size' => 100]);

        $aggsPdv = (new ElasticsearchAggregationStructure('point_of_sales'))
            ->terms('pointofsale_cnpj.keyword', ['size' => 10])
            ->nest($aggsOperator);

        return (new ElasticsearchQueryBuilder)
            ->whereIn('service_status', GroupOfStatus::VALID_SALES)
            ->where('service_sector', Operations::TELECOMMUNICATION)
            ->size(0)
            ->aggregations($aggsPdv)
            ->get();
    }
}
