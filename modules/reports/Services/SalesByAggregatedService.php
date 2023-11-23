<?php

namespace Reports\Services;

use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class SalesByAggregatedService
{
    const POINT_OF_SALE_DETAIL    = 'POINT_OF_SALE_DETAIL';
    const SIZE_QUANTITY_IMPORTANT = 2000;
    protected $repository;

    public function __construct(SaleReportRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getResume(array $filters, $groups)
    {
        $query = $this->getQuery($groups);
        if (! data_get($filters, 'saleStatus')) {
            data_set($filters, 'saleStatus', GroupOfStatus::VALID_SALES);
        }
        $filteredQuery = new DefaultPerformanceCriteria($filters);

        $collection = $this->repository->getFilteredByContext($filteredQuery->apply($query));
        $result     = $collection->toArray();

        $pointsfOfSaleBuckets = $result["aggregations"][self::POINT_OF_SALE_DETAIL]['buckets'];

        $listOfPointsOfSale = [];
        foreach ($pointsfOfSaleBuckets as $pointOfSale) {
            $groupOfOperation = [];
            foreach ($groups as $group => $values) {
                $total = 0;

                foreach ($pointOfSale[$group]['buckets'] as $value) {
                    $total += $value['doc_count'];
                }

                $groupOfOperation += [$group => $total];
            }

            $pointOfSaleItem = [
                'cnpj' => $pointOfSale['key'],
                'total' => $pointOfSale['doc_count'],
                'groups' => $groupOfOperation
            ];

            array_push($listOfPointsOfSale, $pointOfSaleItem);
        }

        return $listOfPointsOfSale;
    }

    private function getQuery($groups)
    {
        $aggregations = [];

        foreach ($groups as $group => $values) {
            $groupAggregation = [
                $group => [
                    'terms' => [
                        'field' => 'service_operation.keyword',
                        'size' => 1000,
                        'include' => $values,
                        'order' =>
                            [
                                '_count' => 'desc',
                            ],
                    ]
                ]
            ];

            $aggregations += $groupAggregation;
        }

        $aggregation = [
            self::POINT_OF_SALE_DETAIL => [
                'terms' => [
                    'field' => 'pointofsale_cnpj.keyword',
                    'size' => self::SIZE_QUANTITY_IMPORTANT,

                ],
                'aggs' => $aggregations
            ]
        ];

        $aggregation = (new ElasticsearchAggregationStructure('operation_count'))->raw($aggregation);

        return (new ElasticsearchQueryBuilder)
            ->size(0)
            ->aggregations($aggregation)
            ->get();
    }
}
