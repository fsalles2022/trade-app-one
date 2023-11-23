<?php

namespace Reports\Services;

use Reports\Adapters\QueryResults\FilterSalesByOperatorAdapter;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\FilterSalesOperations;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class FilterSalesByOperatorService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getFilterSalesByOperator(string $operator, array $filters): array
    {
        $query = $this->getQuery($operator, ConstantHelper::getValue(FilterSalesOperations::class, $operator));
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = new DefaultPerformanceCriteria($filters);

        $result = $this->saleReportRepository->getFilteredByContext($filteredQuery->apply($query));

        return FilterSalesByOperatorAdapter::adapt($operator, $result);
    }

    private function getQuery(string $operator, array $operations): ElasticsearchQueryBuilder
    {
        $aggsSumPrices = (new ElasticsearchAggregationStructure('sum_price'))
            ->sum('service_price');

        $aggsOperation = (new ElasticsearchAggregationStructure('operation'))
            ->terms('service_operation.keyword')
            ->nest($aggsSumPrices);

        return (new ElasticsearchQueryBuilder)
            ->where('service_operator.keyword', $operator)
            ->whereIn('service_operation', $operations)
            ->aggregations($aggsOperation)
            ->size(0)
            ->get();
    }
}
