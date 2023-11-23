<?php

namespace Reports\Services;

use Reports\Adapters\QueryResults\TopPointsOfSaleByOperationAdapter;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Criteria\MonthSalesCriteria;
use Reports\Enum\GroupOfStatus;
use Reports\Enum\PreControlePosLineActivationOperations;
use Reports\Helpers\ReportDateHelper;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;
use TradeAppOne\Domain\Services\PointOfSaleReaderService;

class TopPointsOfSaleByOperationService
{
    private $saleReportRepository;
    private $pointOfSaleReaderService;

    public function __construct(
        SaleReportRepository $saleReportRepository,
        PointOfSaleReaderService $pointOfSaleReaderService
    ) {
        $this->saleReportRepository     = $saleReportRepository;
        $this->pointOfSaleReaderService = $pointOfSaleReaderService;
    }

    public function getSales(array $filters, int $top = 10)
    {
        $query = $this->getQuery();
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);
        $periodQuery   = (new MonthSalesCriteria($filters))->applyIfDatesNotExists($filteredQuery);
        $collection    = $this->saleReportRepository->getFilteredByContext($periodQuery);

        $title        = ReportDateHelper::periodWithCriteriaMonthly($filters);
        $title['top'] = $top;

        return array_merge(
            ['title' => trans('reports::chartnames.column.top_pdvs_by_operation', $title)],
            resolve(TopPointsOfSaleByOperationAdapter::class)->adapt($collection)
        );
    }

    private function getQuery()
    {
        $sumPrice = (new ElasticsearchAggregationStructure('REVENUES'))
            ->sum('service_price');

        $prePlan = (new ElasticsearchAggregationStructure('PRE_PAGO'))
            ->filterTermByFieldValue('service_operation.keyword', PreControlePosLineActivationOperations::PRE)
            ->nest($sumPrice);


        $posPlan = (new ElasticsearchAggregationStructure('POS_PAGO'))
            ->filterTermByFieldValue('service_operation.keyword', PreControlePosLineActivationOperations::POS)
            ->nest($sumPrice)
            ->brother($prePlan);

        $controlePos = (new ElasticsearchAggregationStructure('CONTROLE'))
            ->filterTermByFieldValue('service_operation.keyword', PreControlePosLineActivationOperations::CONTROLE)
            ->nest($sumPrice)
            ->brother($posPlan);

        $aggsPdv = (new ElasticsearchAggregationStructure('POINTS_OF_SALES'))
            ->terms('pointofsale_cnpj.keyword', ['size' => 10])
            ->nest($controlePos);

        return (new ElasticsearchQueryBuilder)
            ->whereIn('service_operator', array_keys(Operations::TELECOMMUNICATION_OPERATORS))
            ->whereIn('service_status', GroupOfStatus::VALID_SALES)
            ->size(0)
            ->aggregations($aggsPdv)
            ->get();
    }
}
