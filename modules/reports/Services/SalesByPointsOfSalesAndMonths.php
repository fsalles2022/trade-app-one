<?php

namespace Reports\Services;

use Carbon\Carbon;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class SalesByPointsOfSalesAndMonths
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getSales(array $filters)
    {
        $filters['months'] = $this->getMonthlyPeriods($filters['months']);

        $query = $this->getQuery($filters);

        $response      = $this->saleReportRepository->getFilteredByContext($query);
        $responseArray = $response->toArray();

        $buckets = $responseArray['aggregations']['PointsOfSale']['buckets'];

        $goals = [];
        foreach ($buckets as $bucketsPointsOfSale) {
            $goalMonthly = [];

            foreach ($filters['months'] as $month) {
                $doc_count                      = data_get($bucketsPointsOfSale, $month['string'].'.buckets.0.doc_count', 0);
                $goalMonthly[$month['numeric']] = $doc_count;
            }

            $goalByPdv = [
                'cnpj' => data_get($bucketsPointsOfSale, 'key', 0),
                'goals' => $goalMonthly
            ];

            array_push($goals, $goalByPdv);
        }

        return $goals;
    }

    private function getQuery(array $filters)
    {
        $months = $filters['months'];
        $cnpjs  = $filters['cnpjs'];

        $aggsMonths = [];

        foreach ($months as $month) {
            $monthStructure = (new ElasticsearchAggregationStructure($month['string']))
                ->range('created_at', ['ranges' => [['from' => $month['start'], 'to' => $month['end']]]]);

            array_push($aggsMonths, $monthStructure);
        }

        $brothers = (new ElasticsearchAggregationStructure('PointsOfSale'))
            ->brothers($aggsMonths);

        $aggs = (new ElasticsearchAggregationStructure('PointsOfSale'))
            ->terms('pointofsale_cnpj.keyword', ['size' => 5000])
            ->nest($brothers);

        return (new ElasticsearchQueryBuilder)
            ->whereIn('service_status', GroupOfStatus::VALID_SALES)
            ->whereIn('pointofsale_cnpj.keyword', $cnpjs, 'OR')
            ->size(0)
            ->aggregations($aggs)
            ->get();
    }

    private function getMonthlyPeriods(array $months)
    {
        $monthlyPeriods = [];

        foreach ($months as $month) {
            $first = Carbon::create(now()->year, $month, 1)->firstOfMonth();
            $end   = Carbon::create(now()->year, $month, 1)->endOfMonth();

            array_push($monthlyPeriods, [
                'string' => $first->format('F-Y'),
                'numeric' => $month,
                'start' => $first->toIso8601String(),
                'end' => $end->toIso8601String()
            ]);
        }
        return $monthlyPeriods;
    }
}
