<?php

namespace Reports\Services;

use Reports\Criteria\DefaultPerformanceCriteria;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Helpers\MoneyHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class TotalSalesTriangulationService
{
    /**
     * @var SaleReportRepository
     */
    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function getSalesTriangulations(array $filters, array $period = null)
    {
        $query         = $this->getQuery($period);
        $filteredQuery = (new DefaultPerformanceCriteria($filters))->apply($query);
        $collections   = $this->saleReportRepository->getFilteredByContext($filteredQuery);

        return $this->adapter($collections);
    }

    public function getQuery($period)
    {
        $price = (new ElasticsearchAggregationStructure('PRICE'))
            ->sum('service_price');

        $with = (new ElasticsearchAggregationStructure('WITH_TRIANGULATION'))
            ->filterExists('service_discount_discount')
            ->nest($price);

        $missing = [
            "WITHOUT_TRIANGULATION" => [
                "missing" => [
                    "field" => "service_discount_discount"
                ]
            ]
        ];

        $without = (new ElasticsearchAggregationStructure('WITHOUT_TRIANGULATION'))
            ->raw($missing)
            ->nest($price)
            ->brother($with);

        $aggsTotal = (new ElasticsearchAggregationStructure('sum_price'))
            ->brother($without)
            ->sum('service_price');


        $query = (new ElasticsearchQueryBuilder())
            ->whereIn('service_status', GroupOfStatus::VALID_SALES)
            ->where('service_sector', Operations::TELECOMMUNICATION)
            ->size(0)
            ->aggregations($aggsTotal)
            ->get();

        return $query;
    }

    private function adapter($collection)
    {
        $aggs         = data_get($collection, 'aggregations');
        $withoutQtd   = data_get($aggs, 'WITHOUT_TRIANGULATION.doc_count');
        $withoutPrice = data_get($aggs, 'WITHOUT_TRIANGULATION.PRICE.value');

        $withQtd    = data_get($aggs, 'WITH_TRIANGULATION.doc_count');
        $withPrice  = data_get($aggs, 'WITH_TRIANGULATION.PRICE.value');
        $totalPrice = data_get($collection, 'aggregations.sum_price.value', 0);

        return [
            'withTriangulation'    => [
                'name'     => 'Com Triangulção',
                'quantity' => number_format($withQtd, '0', '.', '.'),
                'price'    => MoneyHelper::formatMoney($withPrice)
            ],
            'withoutTriangulation' => [
                'name'     => 'Sem Triangulção',
                'quantity' => number_format($withoutQtd, '0', '.', '.'),
                'price'    => MoneyHelper::formatMoney($withoutPrice)
            ],
            'total'                => [
                'quantity' => number_format(data_get($collection, 'hits.total', 0), '0', '.', '.'),
                'price'    => MoneyHelper::formatMoney($totalPrice)
            ]
        ];
    }
}
