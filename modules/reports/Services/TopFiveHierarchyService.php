<?php

namespace Reports\Services;

use Reports\Adapters\QueryResults\TopFiveHierarchyAdapter;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Criteria\PeriodCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class TopFiveHierarchyService
{
    const HIERARCHIES = 'hierarchies';
    /**
     * @var SaleReportRepository
     */
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
        $filteredQuery      = (new DefaultPerformanceCriteria($filters))->apply($query);
        $endDate            = data_get($filters, 'endDate', now());
        $startDate          = data_get($filters, 'startDate', now()->startOfMonth());
        $filteredQuery      = (new PeriodCriteria($startDate, $endDate))->apply($filteredQuery);
        $collection         = $this->saleReportRepository->getFilteredByContext($filteredQuery);
        $elasticSearchArray = $collection->toArray();
        $adapter            = TopFiveHierarchyAdapter::adapt($elasticSearchArray);

        return [
            'title' => "Top 5 Regionais",
            'data'  => $adapter
        ];
    }

    private function getQuery()
    {
        $aggsPlanPre = (new ElasticsearchAggregationStructure('PRE_PAGO'))
            ->terms('service_operation.keyword', [
                'include' => [Operations::VIVO_PRE, Operations::CLARO_PRE]
            ]);

        $aggsPos = (new ElasticsearchAggregationStructure('POS_PAGO'))
            ->terms('service_operation.keyword', ['exclude' => [Operations::VIVO_PRE, Operations::CLARO_PRE]])
            ->brother($aggsPlanPre);

        $aggsPdv = (new ElasticsearchAggregationStructure('hierarchies'))
            ->terms('pointofsale_hierarchy_slug.keyword', ['size' => 5])
            ->nest($aggsPos);

        return (new ElasticsearchQueryBuilder)
            ->whereIn('service_operator', array_keys(Operations::TELECOMMUNICATION_OPERATORS))
            ->size(0)
            ->aggregations($aggsPdv)
            ->get();
    }
}
