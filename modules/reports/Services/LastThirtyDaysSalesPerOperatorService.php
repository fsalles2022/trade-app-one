<?php

namespace Reports\Services;

use Carbon\Carbon;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class LastThirtyDaysSalesPerOperatorService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getLastThirtyDaysSalesPerOperator(array $filters)
    {
        $query = $this->getQuery();
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);

        $collection = $this->saleReportRepository->getFilteredByContext($filteredQuery);

        $elasticSearchArray = $collection->toArray();
        $buckets            = data_get($elasticSearchArray, 'aggregations.sales_over_time.buckets');
        $data               = [];
        $operators          = [];

        foreach ($buckets as $key => $dateBucket) {
            $date = Carbon::createFromFormat('Y-m-d', $dateBucket['key_as_string'])->format('d/m/Y');
            foreach ($dateBucket['operator']['buckets'] as $operatorBucket) {
                $operator                                = $operatorBucket['key'];
                $data[$date][$operator]                  = $operatorBucket;
                $dateDocCount                            = data_get($dateBucket, 'doc_count');
                $data[$date][$operator]['share']         =
                    $dateDocCount > 0 ? data_get($operatorBucket, 'doc_count') / $dateDocCount : 0;
                $totalCount                              = data_get($elasticSearchArray, 'hits.total');
                $data[$date][$operator]['participation'] =
                    $totalCount > 0 ? (data_get($operatorBucket, 'doc_count') / $totalCount) : 0;
                if (! in_array($operator, $operators)) {
                    array_push($operators, $operator);
                }
            }
            $data[$date]['TOTAL'] = [
                'key'             => 'TOTAL',
                'doc_count'       => data_get($dateBucket, 'doc_count', 0),
                'sum_price'       => ['value' => data_get($dateBucket, 'sum_day.value', 0)],
                'average_balance' => ['value' => data_get($dateBucket, 'average_day.value', 0)],
                'share'           => data_get($dateBucket, 'doc_count') > 0 ? 1 : 0,
                'participation'   => (data_get($dateBucket, 'doc_count') / data_get(
                    $elasticSearchArray,
                    'hits.total'
                )) ?? 0
            ];
        }

        array_push($operators, 'TOTAL');

        return [
            'total'     => data_get($elasticSearchArray, 'hits.total'),
            'operators' => $operators,
            'data'      => $data
        ];
    }

    private function getQuery()
    {
        $now                 = Carbon::now();
        $now->tz             = config('app.timezone');
        $last                = clone($now);
        $nowDateString       = $last->toIso8601String();
        $lastMonthDateString = $now->subMonth()->toIso8601String();

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

        $dateAggs = (new ElasticsearchAggregationStructure('sales_over_time'))
            ->dateHistogram('created_at', ['interval' => 'day', 'format' => 'yyyy-MM-dd'])
            ->nest($aggsPricesDay);

        return (new ElasticsearchQueryBuilder)
            ->where('created_at', "[{$lastMonthDateString} TO {$nowDateString}]")
            ->size(0)
            ->aggregations($dateAggs)
            ->get();
    }
}
