<?php

namespace Reports\Services;

use Carbon\Carbon;
use Reports\Enum\GroupOfStatus;
use Reports\SubModules\Hourly\Helpers\CriteriaHourlyDminus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class SalesByMonthAndPeriod
{
    const DAY    = 'CONSOLIDATE_OPERATORS_DAY';
    const MONTH  = 'CONSOLIDATE_OPERATORS_MONTH';
    const DMINUS = 'CONSOLIDATE_OPERATORS_DMINUS';

    const OPERATORS  = 'CONSOLIDATE_OPERATORS';
    const OPERATIONS = 'CONSOLIDATE_OPERATIONS';

    const POINT_OF_SALE_DETAIL = 'POINT_OF_SALE_DETAIL';

    const CONSOLIDATE_OPERATORS_DAY    = 'CONSOLIDATE_OPERATORS_DAY';
    const CONSOLIDATE_OPERATORS_DMINUS = 'CONSOLIDATE_OPERATORS_DMINUS';
    const CONSOLIDATE_OPERATORS_MONTH  = 'CONSOLIDATE_OPERATORS_MONTH';

    protected $repository;
    /**
     * @var Carbon
     */
    protected $day;
    protected $minusDay;
    protected $minusMonth;

    const SIZE_QUANTITY_IMPORTANT = 2000;

    public function __construct(SaleReportRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getResume(array $filters, CriteriaHourlyDminus $strategy)
    {
        $this->day        = $strategy->day ?? now();
        $this->minusDay   = $strategy->strategy ?? 1;
        $this->minusMonth = data_get($filters, 'minusMonth', 1);
        $pointsfOfSale    = data_get($filters, 'pointsOfSale', []);

        $query      = $this->getQuery($strategy, $pointsfOfSale);
        $collection = $this->repository->executeWithoutContext($query);
        $result     = $collection->toArray();

        return [
            'total' => $result['hits']['total'],
            'data'  => $result["aggregations"] ?? []
        ];
    }

    private function getQuery(CriteriaHourlyDminus $strategy, array $pointsfOfSale)
    {
        $rangeDminus = [
            'field'     => 'created_at',
            'format'    => 'YYYY-MM-dd H-m-s',
            'ranges'    => [
                [
                    'to'   => $strategy->dMinusEnd->format('Y-m-d H-i-s'),
                    'from' => $strategy->dMinusStart->format('Y-m-d H-i-s'),
                ],
            ],
            'time_zone' => 'America/Sao_Paulo',
        ];
        $rangeDay    = [
            'field'     => 'created_at',
            'format'    => 'YYYY-MM-dd',
            'ranges'    => [
                [
                    'from' => $strategy->day->format('Y-m-d'),
                ],
            ],
            'time_zone' => 'America/Sao_Paulo',
        ];
        $rangeMonth  = [
            'field'     => 'created_at',
            'format'    => 'YYYY-MM-dd',
            'ranges'    => [
                [
                    'from' => $this->day->copy()->startOfMonth()->toDateString(),
                ],
            ],
            'time_zone' => 'America/Sao_Paulo',
        ];

        $aggreggationToSumByServicePrice = [
            'prices' => [
                'sum' => [
                    'field' => 'service_price'
                ]
            ]
        ];

        $aggregtionByOperatorAndOperation = [
            self::OPERATORS => [
                'terms' => [
                    'field' => 'service_operator.keyword',
                    'size'  => 10,
                    'order' =>
                        [
                            '_count' => 'desc',
                        ],
                ],
                'aggs'  => [
                    self::OPERATIONS => [
                        'terms' => [
                            'field' => 'service_operation.keyword',
                            'size'  => 10,
                            'order' =>
                                [
                                    '_count' => 'desc',
                                ],
                        ],
                        'aggs' => $aggreggationToSumByServicePrice
                    ],
                ],
            ],
        ];

        $aggregation = [
            self::POINT_OF_SALE_DETAIL         => [
                'terms' => [
                    'field' => 'pointofsale_cnpj.keyword',
                    'size'  => self::SIZE_QUANTITY_IMPORTANT,

                ],
                'aggs'  => [
                    self::DAY    => [
                        'date_range' => $rangeDay,
                        'aggs'       => $aggregtionByOperatorAndOperation,
                    ],
                    self::DMINUS => [
                        'date_range' => $rangeDminus,
                        'aggs'       => $aggregtionByOperatorAndOperation,
                    ],
                    self::MONTH  => [
                        'date_range' => $rangeMonth,
                        'aggs'       => $aggregtionByOperatorAndOperation,
                    ],
                ],
            ],
            self::CONSOLIDATE_OPERATORS_DAY    => [
                'date_range' => $rangeDay,
                'aggs'       => $aggregtionByOperatorAndOperation,
            ],
            self::CONSOLIDATE_OPERATORS_DMINUS => [
                'date_range' => $rangeDminus,
                'aggs'       => $aggregtionByOperatorAndOperation
            ],
            self::CONSOLIDATE_OPERATORS_MONTH  => [
                'date_range' => $rangeMonth,
                'aggs'       => $aggregtionByOperatorAndOperation
            ],
        ];

        $aggregation = (new ElasticsearchAggregationStructure('operation_count'))->raw($aggregation);

        return (new ElasticsearchQueryBuilder)
            ->whereIn('service_status', GroupOfStatus::VALID_SALES)
            ->where('service_sector', Operations::TELECOMMUNICATION)
            ->whereIn('pointofsale_cnpj', $pointsfOfSale)
            ->size(0)
            ->aggregations($aggregation)
            ->get();
    }
}
