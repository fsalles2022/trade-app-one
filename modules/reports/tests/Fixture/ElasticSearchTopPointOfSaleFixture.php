<?php

namespace Reports\Tests\Fixture;

class ElasticSearchTopPointOfSaleFixture
{
    public static function getSaleArray()
    {
        return [
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
                    'total' => 602765,
                    'max_score' => 0,
                    'hits' =>
                        array (
                        ),
                ),
                'aggregations' =>
                array (
                    'point_of_sales' =>
                        array (
                            'doc_count_error_upper_bound' => 3074,
                            'sum_other_doc_count' => 598147,
                            'buckets' =>
                                array (
                                    0 =>
                                        array (
                                            'key' => '33200056002001',
                                            'doc_count' => 4618,
                                            'operators' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CLARO',
                                                                    'doc_count' => 3007,
                                                                    'plan_pre' =>
                                                                        array (
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array (
                                                                                    0 =>
                                                                                        array (
                                                                                            'key' => 'CLARO_PRE',
                                                                                            'doc_count' => 1274,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                    'plan_pos' =>
                                                                        array (
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array (
                                                                                    0 =>
                                                                                        array (
                                                                                            'key' => 'CONTROLE_BOLETO',
                                                                                            'doc_count' => 967,
                                                                                        ),
                                                                                    1 =>
                                                                                        array (
                                                                                            'key' => 'CONTROLE_FACIL',
                                                                                            'doc_count' => 766,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'TIM',
                                                                    'doc_count' => 1005,
                                                                    'plan_pre' =>
                                                                        array (
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array (
                                                                                ),
                                                                        ),
                                                                    'plan_pos' =>
                                                                        array (
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array (
                                                                                    0 =>
                                                                                        array (
                                                                                            'key' => 'TIM_EXPRESS',
                                                                                            'doc_count' => 557,
                                                                                        ),
                                                                                    1 =>
                                                                                        array (
                                                                                            'key' => 'TIM_CONTROLE_FATURA',
                                                                                            'doc_count' => 448,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            2 =>
                                                                array (
                                                                    'key' => 'VIVO',
                                                                    'doc_count' => 331,
                                                                    'plan_pre' =>
                                                                        array (
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array (
                                                                                    0 =>
                                                                                        array (
                                                                                            'key' => 'VIVO_PRE',
                                                                                            'doc_count' => 45,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                    'plan_pos' =>
                                                                        array (
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array (
                                                                                    0 =>
                                                                                        array (
                                                                                            'key' => 'CONTROLE',
                                                                                            'doc_count' => 224,
                                                                                        ),
                                                                                    1 =>
                                                                                        array (
                                                                                            'key' => 'CONTROLE_CARTAO',
                                                                                            'doc_count' => 62,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            3 =>
                                                                array (
                                                                    'key' => 'OI',
                                                                    'doc_count' => 275,
                                                                    'plan_pre' =>
                                                                        array (
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array (
                                                                                ),
                                                                        ),
                                                                    'plan_pos' =>
                                                                        array (
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array (
                                                                                    0 =>
                                                                                        array (
                                                                                            'key' => 'OI_CONTROLE_CARTAO',
                                                                                            'doc_count' => 256,
                                                                                        ),
                                                                                    1 =>
                                                                                        array (
                                                                                            'key' => 'OI_CONTROLE_BOLETO',
                                                                                            'doc_count' => 19,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                        ),
                                                ),
                                        ),
                                ),
                        ),
                ),
        ];
    }
}
