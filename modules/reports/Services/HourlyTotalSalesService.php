<?php

namespace Reports\Services;

use Carbon\Carbon;
use Reports\Adapters\QueryResults\HourlyTotalSalesAdapter;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class HourlyTotalSalesService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getHourly(array $filters)
    {
        $query = $this->getQuery();
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);

        $collection    = $this->saleReportRepository->getFilteredByContext($filteredQuery);
        $since         = (Carbon::now())->startOfDay()->hour;
        $nowDateString = (Carbon::now())->hour;
        $adapted       = HourlyTotalSalesAdapter::adapt($collection->toArray());
        $totalAtNow    = 0;
        $amountAtNow   = 0;
        $adapted       = $adapted->map(function ($item) use (&$totalAtNow, &$amountAtNow) {
            $totalAtNow        += $item['TOTAL'];
            $amountAtNow       += $item['AMOUNT'];
            $item['ACC_TOTAL']  = $totalAtNow;
            $item['ACC_AMOUNT'] = $amountAtNow;
            return $item;
        });
        return [
            'since'    => $since,
            'title'    => trans('reports::chartnames.line.hourly_acc', ['day' => now()->format('d/m/Y')]),
            'now'      => now()->toDateTimeString(),
            'totalNow' => $nowDateString,
            'data'     => $adapted
        ];
    }

    private function getQuery()
    {
        $since         = (Carbon::now())->startOfDay()->toIso8601String();
        $nowDateString = (Carbon::now())->toIso8601String();

        $aggsSumPrices = (new ElasticsearchAggregationStructure('sum_price'))
            ->sum('service_price')
            ->averageToSameParentLevel('service_price');

        $aggsOperator = (new ElasticsearchAggregationStructure('operator'))
            ->terms('service_operator.keyword')
            ->nest($aggsSumPrices);

        $aggsPricesDay = (new ElasticsearchAggregationStructure('sum_day'))
            ->sum('service_price')
            ->averageToSameParentLevel('service_price', 'average_day')
            ->brother($aggsOperator);

        $dateAggs = (new ElasticsearchAggregationStructure('sales_over_day'))
            ->dateHistogram('created_at', ['interval' => 'hour', 'format' => 'H'], 'America/Sao_Paulo')
            ->nest($aggsPricesDay);

        return (new ElasticsearchQueryBuilder)
            ->where('created_at', "[{$since} TO {$nowDateString}]")
            ->size(0)
            ->aggregations($dateAggs)
            ->get();
    }
}
