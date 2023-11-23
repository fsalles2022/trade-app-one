<?php

namespace Reports\Services;

use Carbon\Carbon;
use Reports\Criteria\DefaultCriteria;
use Reports\Enum\GroupOfStatus;
use Reports\SubModules\Hourly\Constants\PrePosLineActivationOperations;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class SalesByGroupOfOperationsService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getSalesByGroupOfOperations(array $filters, array $period = null)
    {
        $query = $this->getQuery($period);
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = (new DefaultCriteria($filters))->apply($query);
        $collection    = $this->saleReportRepository->getFilteredByContext($filteredQuery);

        $elasticSearchArray = $collection->toArray();
        $operators          = data_get($elasticSearchArray, 'aggregations.operator.buckets');
        $operationsMapped   = [];
        foreach ($operators as $operator) {
            $map['operator'] = $operator['key'];
            $posPago         = collect(data_get($operator, 'POS_PAGO.buckets', []));
            $totalPos        = $posPago->sum('doc_count');
            $totalPosPrices  = $posPago->sum('prices.value');

            $prePago        = collect(data_get($operator, 'PRE_PAGO.buckets', []));
            $totalPre       = $prePago->sum('doc_count');
            $totalPrePrices = $prePago->sum('prices.value');

            $map['posPago']['quantity'] = $totalPos;
            $map['posPago']['prices']   = $totalPosPrices;

            $map['prePago']['quantity'] = $totalPre;
            $map['prePago']['prices']   = $totalPrePrices;

            $map['total'] ['quantity'] = data_get($operator, 'doc_count');
            $map['total'] ['prices']   = $totalPrePrices + $totalPosPrices;
            array_push($operationsMapped, $map);
        }
        $collectionOfOperators = collect($operationsMapped);

        $resume['operators']           = $operationsMapped;
        $resume['posPago']['quantity'] = $collectionOfOperators->sum('posPago.quantity');
        $resume['posPago']['prices']   = $collectionOfOperators->sum('posPago.prices');

        $resume['prePago']['quantity'] = $collectionOfOperators->sum('prePago.quantity');
        $resume['prePago']['prices']   = $collectionOfOperators->sum('prePago.prices');

        $resume['total']['quantity'] = $collectionOfOperators->sum('total.quantity');
        $resume['total']['prices']   = data_get($elasticSearchArray, 'aggregations.prices.value');

        return $resume;
    }

    private function getQuery($period)
    {
        $priceAgg    = [
            'prices' => [
                'sum' => [
                    'field' => 'service_price'
                ]
            ]
        ];
        $aggregation = [
            'prices'   => [
                'sum' => [
                    'field' => 'service_price'
                ]
            ],
            'operator' => [
                'terms' => [
                    'field' => 'service_operator.keyword',
                    'size'  => 1000,
                ],
                'aggs'  => [
                    'POS_PAGO' => [
                        'terms' => [
                            'field'   => 'service_operation.keyword',
                            'size'    => 1000,
                            'include' => PrePosLineActivationOperations::POS,
                            'exclude' => PrePosLineActivationOperations::PRE
                        ],
                        'aggs'  => $priceAgg
                    ],
                    'PRE_PAGO' => [
                        'terms' => [
                            'field'   => 'service_operation.keyword',
                            'include' => PrePosLineActivationOperations::PRE,
                            'exclude' => PrePosLineActivationOperations::POS
                        ],
                        'aggs'  => $priceAgg
                    ]
                ]
            ]
        ];

        $aggregation = (new ElasticsearchAggregationStructure('operator'))->raw($aggregation);

        $since = data_get($period, 'since', (Carbon::now())->startOfMonth()->toIso8601String());
        $until = data_get($period, 'until', (Carbon::now())->toIso8601String());

        return (new ElasticsearchQueryBuilder)
            ->where('created_at', "[{$since} TO {$until}]")
            ->where('service_sector', Operations::TELECOMMUNICATION)
            ->size(0)
            ->aggregations($aggregation)
            ->get();
    }
}
