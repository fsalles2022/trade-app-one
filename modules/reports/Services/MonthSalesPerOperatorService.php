<?php

namespace Reports\Services;

use Carbon\Carbon;
use Reports\Criteria\DefaultCriteria;
use Reports\Criteria\MonthSalesCriteria;
use Reports\Enum\GroupOfStatus;
use Reports\Helpers\ReportDateHelper;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class MonthSalesPerOperatorService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getMonthSalesPerOperator(array $filters)
    {
        $query         = $this->getQuery();
        $filteredQuery = (new DefaultCriteria($filters))->apply($query);
        $periodQuery   = (new MonthSalesCriteria($filters))->apply($filteredQuery);

        $collection = $this->saleReportRepository->getFilteredByContext($periodQuery);

        $elasticSearchArray = $collection->toArray();
        $buckets            = data_get($elasticSearchArray, 'aggregations.sales_over_time.buckets');
        $data               = [];
        $operators          = [];

        foreach ($buckets as $key => $dateBucket) {
            $date = Carbon::createFromFormat('Y-m-d', $dateBucket['key_as_string'])->format('d/m/Y');
            foreach ($dateBucket['operator']['buckets'] as $operatorBucket) {
                $operator                                = $operatorBucket['key'];
                $data[$date][$operator]                  = $operatorBucket;
                $data[$date][$operator]['share']         =
                    (data_get($operatorBucket, 'doc_count') / data_get($dateBucket, 'doc_count')) ?? 0;
                $data[$date][$operator]['participation'] =
                    (data_get($operatorBucket, 'doc_count') / data_get($elasticSearchArray, 'hits.total')) ?? 0;
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

        $period = ReportDateHelper::periodWithCriteriaMonthly($filters, true);

        return [
            'total'     => data_get($elasticSearchArray, 'hits.total'),
            'startDate' => $period['startDate'],
            'endDate'   => $period['endDate'],
            'operators' => $operators,
            'data'      => $data
        ];
    }

    private function getQuery()
    {
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
            ->whereIn('service_status', GroupOfStatus::VALID_SALES)
            ->size(0)
            ->aggregations($dateAggs)
            ->get();
    }
}
