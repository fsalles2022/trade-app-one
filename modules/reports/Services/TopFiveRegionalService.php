<?php

namespace Reports\Services;

use Reports\Adapters\QueryResults\TopFiveRegionalAdapter;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Criteria\MonthSalesCriteria;
use Reports\Enum\GroupOfStatus;
use Reports\Enum\PreControlePosLineActivationOperations;
use Reports\Helpers\ReportDateHelper;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Enumerators\GroupOfOperations;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class TopFiveRegionalService
{
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function get(array $filters = [])
    {
        $query = $this->getQuery();
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);
        $periodQuery   = (new MonthSalesCriteria($filters))->applyIfDatesNotExists($filteredQuery);
        $collection    = $this->saleReportRepository->getFilteredByContext($periodQuery);

        $title = $title = ReportDateHelper::periodWithCriteriaMonthly($filters);

        return array_merge(
            ['title' => trans('reports::chartnames.column.top_five_hierarchy', $title)],
            TopFiveRegionalAdapter::adapt($collection)
        );
    }

    private function getQuery()
    {
        $aggsPlanPre = (new ElasticsearchAggregationStructure(GroupOfOperations::PRE_PAGO))
            ->filterTermByFieldValue('service_operation.keyword', PreControlePosLineActivationOperations::PRE);

        $aggsPlanPos = (new ElasticsearchAggregationStructure(GroupOfOperations::POS_PAGO))
            ->filterTermByFieldValue('service_operation.keyword', PreControlePosLineActivationOperations::POS)
            ->brother($aggsPlanPre);

        $aggsControle = (new ElasticsearchAggregationStructure(GroupOfOperations::CONTROLE))
            ->filterTermByFieldValue('service_operation.keyword', PreControlePosLineActivationOperations::CONTROLE)
            ->brother($aggsPlanPos);

        $aggsPdv = (new ElasticsearchAggregationStructure('hierarchies'))
            ->terms('pointofsale_hierarchy_label.keyword', ['size' => 5])
            ->nest($aggsControle);

        return (new ElasticsearchQueryBuilder)
            ->whereIn('service_operator', array_keys(Operations::TELECOMMUNICATION_OPERATORS))
            ->size(0)
            ->aggregations($aggsPdv)
            ->get();
    }
}
