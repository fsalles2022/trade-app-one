<?php

namespace Reports\Tests\Fixture;

class ElasticSearchTotalSalesTriangulationFixture
{
    public static function fixture()
    {
        return array (
            'took' => 51,
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
                    'total' => 1226944,
                    'max_score' => 0,
                    'hits' =>
                        array (
                        ),
                ),
            'aggregations' =>
                array (
                    'WITH_TRIANGULATION' =>
                        array (
                            'doc_count' => 947,
                            'PRICE' =>
                                array (
                                    'value' => 47008.23143768310546875,
                                ),
                        ),
                    'WITHOUT_TRIANGULATION' =>
                        array (
                            'doc_count' => 1225997,
                            'PRICE' =>
                                array (
                                    'value' => 40111405.000152587890625,
                                ),
                        ),
                    'sum_price' =>
                        array (
                            'value' => 40158413.23159027099609375,
                        ),
                ),
        );
    }
}
