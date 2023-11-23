<?php

namespace Reports\Services;

use Carbon\Carbon;
use Reports\Adapters\QueryResults\SalesByNetworkPlansTelecommunicationAdapter;
use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use Reports\SubModules\Hourly\Constants\PrePosLineActivationOperations;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\GroupOfOperations;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Repositories\Collections\NetworkRepository;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class SalesByNetworkPlansTelecommunicationService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;
    protected $networkRepository;

    public function __construct(SaleReportRepository $saleReportRepository, NetworkRepository $networkRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
        $this->networkRepository    = $networkRepository;
    }

    public function getSalesByNetwork(array $filters)
    {
        $query = $this->getQuery($filters);

        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);
        $collection    = $this->saleReportRepository->executeWithoutContext($filteredQuery);

        $period = $this->getPeriod($filters);

        return array_merge(
            ['title' => trans(
                'reports::chartnames.column.sales_by_network_plans_telecommunication',
                $period
            )],
            SalesByNetworkPlansTelecommunicationAdapter::adapt($collection)
        );
    }

    private function getQuery($filters)
    {
        $pre_plan = (new ElasticsearchAggregationStructure(GroupOfOperations::PRE_PAGO))
            ->filterTermByFieldValue('service_operation.keyword', PrePosLineActivationOperations::PRE);

        $plans = (new ElasticsearchAggregationStructure(GroupOfOperations::POS_PAGO))
            ->filterTermByFieldValue('service_operation.keyword', PrePosLineActivationOperations::POS)
            ->brother($pre_plan);

        $aggs = (new ElasticsearchAggregationStructure('networks'))
            ->terms('pointofsale_network_slug.keyword')
            ->nest($plans);

        $query = (new ElasticsearchQueryBuilder)
            ->whereIn('pointofsale_network_slug.keyword', ConstantHelper::getAllConstants(NetworkEnum::class), 'OR')
            ->size(0)
            ->aggregations($aggs);

        if (empty($filters['endDate']) and empty($filters['startDate'])) {
            $period = $this->periodMonthly();

            return $query
                ->where('created_at', "[{$period['startDate']} TO {$period['endDate']}]")
                ->get();
        }

        return $query->get();
    }

    private function periodMonthly(): array
    {
        $since = (Carbon::now())->startOfMonth()->toIso8601String();
        $now   = Carbon::now()->toIso8601String();

        return ['startDate' => $since, 'endDate' => $now];
    }

    private function getPeriod($filters)
    {
        $period = $this->periodMonthly();

        $endDate   = data_get($filters, 'endDate', $period['endDate']);
        $startDate = data_get($filters, 'startDate', $period['startDate']);

        $period = [
            'startDate' => Carbon::parse($startDate)->format('d/m/y'),
            'endDate'   => Carbon::parse($endDate)->format('d/m/y')
        ];

        if (array_key_exists('endDate', $filters) and empty($filters['startDate'])) {
            $period['startDate'] = '';
        }

        return $period;
    }
}
