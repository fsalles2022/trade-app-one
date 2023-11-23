<?php

namespace Reports\Services;

use Carbon\Carbon;
use Reports\Adapters\QueryResults\MonthSalesPerStateAdapter;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class MonthSalesPerStateService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getMonthSalesPerState(array $filters)
    {
        $query = $this->getQuery();
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);

        $collection = $this->saleReportRepository->getFilteredByContext($filteredQuery);

        return array_merge(['title' => trans('reports::chartnames.column.month_sales_per_state')], MonthSalesPerStateAdapter::adapt($collection));
    }

    private function getQuery()
    {
        $now  = Carbon::now();
        $date = $now->copy()->subMonth()->toDateString();

        $aggsOperation = (new ElasticsearchAggregationStructure('operation_count'))
            ->terms('service_operation.keyword');

        $aggsOperator = (new ElasticsearchAggregationStructure('operator_count'))
            ->terms('service_operator.keyword')
            ->nest($aggsOperation);

        $aggs = (new ElasticsearchAggregationStructure('state_count'))
            ->terms('pointofsale_state.keyword', ['size' => 5, 'order' => ['_count' => 'desc']])
            ->nest($aggsOperator);

        return (new ElasticsearchQueryBuilder)
            ->where('created_at', "[{$date} TO {$now->toDateString()}]")
            ->whereIn('service_operator', array_keys(Operations::TELECOMMUNICATION_OPERATORS))
            ->size(5)
            ->aggregations($aggs)
            ->get();
    }
}
