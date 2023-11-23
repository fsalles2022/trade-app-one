<?php

namespace Reports\Tests\Fixture;

class ElasticSearchServicePieChartFixture
{
    public static function getSaleArray()
    {
        return array (
            'took' => 0,
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
                    'total' => 465673,
                    'max_score' => 0,
                    'hits' =>
                        array (
                        ),
                ),
            'aggregations' =>
                array (
                    'operation' =>
                        array (
                            'doc_count_error_upper_bound' => 0,
                            'sum_other_doc_count' => 0,
                            'buckets' =>
                                array (
                                    0 =>
                                        array (
                                            'key' => 'CLARO_PRE',
                                            'doc_count' => 201764,
                                            'sum_price' =>
                                                array (
                                                    'value' => 0,
                                                ),
                                        ),
                                    1 =>
                                        array (
                                            'key' => 'CONTROLE_BOLETO',
                                            'doc_count' => 178454,
                                            'sum_price' =>
                                                array (
                                                    'value' => 8553504.757671356,
                                                ),
                                        ),
                                    2 =>
                                        array (
                                            'key' => 'CONTROLE_FACIL',
                                            'doc_count' => 77457,
                                            'sum_price' =>
                                                array (
                                                    'value' => 3494901.480758667,
                                                ),
                                        ),
                                    3 =>
                                        array (
                                            'key' => 'CLARO_POS',
                                            'doc_count' => 7692,
                                            'sum_price' =>
                                                array (
                                                    'value' => 1034825.319152832,
                                                ),
                                        ),
                                    4 =>
                                        array (
                                            'key' => 'CLARO_VOZ_DADOS',
                                            'doc_count' => 248,
                                            'sum_price' =>
                                                array (
                                                    'value' => 9917.520416259766,
                                                ),
                                        ),
                                    5 =>
                                        array (
                                            'key' => 'CLARO_CONTROLE',
                                            'doc_count' => 56,
                                            'sum_price' =>
                                                array (
                                                    'value' => 2814.4400901794434,
                                                ),
                                        ),
                                    6 =>
                                        array (
                                            'key' => 'CLARO_DADOS',
                                            'doc_count' => 2,
                                            'sum_price' =>
                                                array (
                                                    'value' => 59.97999954223633,
                                                ),
                                        ),
                                ),
                        ),
                ),
        );
    }
}
