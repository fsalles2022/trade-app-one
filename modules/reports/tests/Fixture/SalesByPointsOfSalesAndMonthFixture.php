<?php

namespace Reports\Tests\Fixture;

class SalesByPointsOfSalesAndMonthFixture
{
    protected $month1;
    protected $month2;
    protected $pdv1;
    protected $pdv2;

    public function __construct(array $data)
    {
        $this->month1 = data_get($data, 'month1', 'Date-2000');
        $this->month2 = data_get($data, 'month2', 'Date-2002');
        $this->pdv1   = data_get($data, 'pdv1', '12300000000');
        $this->pdv2   = data_get($data, 'pdv2', '00000000123');
    }

    public function getSaleArray()
    {
        return array (
            'took' => 21,
            'timed_out' => false,
            '_shards' =>
                array (
                    'total' => 5,
                    'successful' => 5,
                    'skipped' => 0,
                    'failed' => 0,
                ),
            'hits' =>
                array (
                    'total' => 623,
                    'max_score' => 0,
                    'hits' =>
                        array (
                        ),
                ),
            'aggregations' =>
                array (
                    'PointsOfSale' =>
                        array (
                            'doc_count_error_upper_bound' => 0,
                            'sum_other_doc_count' => 0,
                            'buckets' =>
                                array (
                                    0 =>
                                        array (
                                            'key' => $this->pdv1,
                                            'doc_count' => 407,
                                            $this->month1 =>
                                                array (
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => '2019-03-01T03:00:00.000Z-2019-04-01T02:59:59.000Z',
                                                                    'from' => 1551409200000,
                                                                    'from_as_string' => '2019-03-01T03:00:00.000Z',
                                                                    'to' => 1554087599000,
                                                                    'to_as_string' => '2019-04-01T02:59:59.000Z',
                                                                    'doc_count' => 68,
                                                                ),
                                                        ),
                                                ),
                                            $this->month2 =>
                                                array (
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => '2019-04-01T03:00:00.000Z-2019-05-01T02:59:59.000Z',
                                                                    'from' => 1554087600000,
                                                                    'from_as_string' => '2019-04-01T03:00:00.000Z',
                                                                    'to' => 1556679599000,
                                                                    'to_as_string' => '2019-05-01T02:59:59.000Z',
                                                                    'doc_count' => 69,
                                                                ),
                                                        ),
                                                ),
                                        ),
                                    1 =>
                                        array (
                                            'key' => $this->pdv2,
                                            'doc_count' => 216,
                                            $this->month1 =>
                                                array (
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => '2019-03-01T03:00:00.000Z-2019-04-01T02:59:59.000Z',
                                                                    'from' => 1551409200000,
                                                                    'from_as_string' => '2019-03-01T03:00:00.000Z',
                                                                    'to' => 1554087599000,
                                                                    'to_as_string' => '2019-04-01T02:59:59.000Z',
                                                                    'doc_count' => 44,
                                                                ),
                                                        ),
                                                ),
                                            $this->month2 =>
                                                array (
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => '2019-04-01T03:00:00.000Z-2019-05-01T02:59:59.000Z',
                                                                    'from' => 1554087600000,
                                                                    'from_as_string' => '2019-04-01T03:00:00.000Z',
                                                                    'to' => 1556679599000,
                                                                    'to_as_string' => '2019-05-01T02:59:59.000Z',
                                                                    'doc_count' => 31,
                                                                ),
                                                        ),
                                                ),
                                        ),
                                ),
                        ),
                ),
        );
    }
}
