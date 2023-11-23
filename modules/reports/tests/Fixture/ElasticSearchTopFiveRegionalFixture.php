<?php

namespace Reports\Tests\Fixture;

class ElasticSearchTopFiveRegionalFixture
{
    public static function getSaleArray()
    {
        return array (
            'took' => 39,
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
                    'total' => 3628,
                    'max_score' => 0,
                    'hits' =>
                        array (
                        ),
                ),
            'aggregations' =>
                array (
                    'hierarchies' =>
                        array (
                            'doc_count_error_upper_bound' => 83,
                            'sum_other_doc_count' => 2615,
                            'buckets' =>
                                array (
                                    0 =>
                                        array (
                                            'key' => 'Regional BEL',
                                            'doc_count' => 191,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CONTROLE',
                                                                    'doc_count' => 49,
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'CONTROLE_BOLETO',
                                                                    'doc_count' => 39,
                                                                ),
                                                            2 =>
                                                                array (
                                                                    'key' => 'TIM_EXPRESS',
                                                                    'doc_count' => 21,
                                                                ),
                                                            3 =>
                                                                array (
                                                                    'key' => 'OI_CONTROLE_CARTAO',
                                                                    'doc_count' => 20,
                                                                ),
                                                            4 =>
                                                                array (
                                                                    'key' => 'CONTROLE_FACIL',
                                                                    'doc_count' => 17,
                                                                ),
                                                            5 =>
                                                                array (
                                                                    'key' => 'OI_CONTROLE_BOLETO',
                                                                    'doc_count' => 10,
                                                                ),
                                                            6 =>
                                                                array (
                                                                    'key' => 'TIM_CONTROLE_FATURA',
                                                                    'doc_count' => 7,
                                                                ),
                                                            7 =>
                                                                array (
                                                                    'key' => 'CONTROLE_CARTAO',
                                                                    'doc_count' => 3,
                                                                ),
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CLARO_PRE',
                                                                    'doc_count' => 24,
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'VIVO_PRE',
                                                                    'doc_count' => 1,
                                                                ),
                                                        ),
                                                ),
                                        ),
                                    1 =>
                                        array (
                                            'key' => 'Regional FOR',
                                            'doc_count' => 162,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CONTROLE',
                                                                    'doc_count' => 22,
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'CONTROLE_FACIL',
                                                                    'doc_count' => 22,
                                                                ),
                                                            2 =>
                                                                array (
                                                                    'key' => 'TIM_CONTROLE_FATURA',
                                                                    'doc_count' => 17,
                                                                ),
                                                            3 =>
                                                                array (
                                                                    'key' => 'CONTROLE_BOLETO',
                                                                    'doc_count' => 16,
                                                                ),
                                                            4 =>
                                                                array (
                                                                    'key' => 'TIM_EXPRESS',
                                                                    'doc_count' => 12,
                                                                ),
                                                            5 =>
                                                                array (
                                                                    'key' => 'CONTROLE_CARTAO',
                                                                    'doc_count' => 7,
                                                                ),
                                                            6 =>
                                                                array (
                                                                    'key' => 'OI_CONTROLE_CARTAO',
                                                                    'doc_count' => 7,
                                                                ),
                                                            7 =>
                                                                array (
                                                                    'key' => 'OI_CONTROLE_BOLETO',
                                                                    'doc_count' => 5,
                                                                ),
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CLARO_PRE',
                                                                    'doc_count' => 53,
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'VIVO_PRE',
                                                                    'doc_count' => 1,
                                                                ),
                                                        ),
                                                ),
                                        ),
                                    2 =>
                                        array (
                                            'key' => 'Regional 1',
                                            'doc_count' => 150,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'NEXTEL_CONTROLE_BOLETO',
                                                                    'doc_count' => 25,
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'TIM_EXPRESS',
                                                                    'doc_count' => 21,
                                                                ),
                                                            2 =>
                                                                array (
                                                                    'key' => 'TIM_CONTROLE_FATURA',
                                                                    'doc_count' => 19,
                                                                ),
                                                            3 =>
                                                                array (
                                                                    'key' => 'OI_CONTROLE_CARTAO',
                                                                    'doc_count' => 15,
                                                                ),
                                                            4 =>
                                                                array (
                                                                    'key' => 'CONTROLE_BOLETO',
                                                                    'doc_count' => 12,
                                                                ),
                                                            5 =>
                                                                array (
                                                                    'key' => 'NEXTEL_CONTROLE_CARTAO',
                                                                    'doc_count' => 8,
                                                                ),
                                                            6 =>
                                                                array (
                                                                    'key' => 'CONTROLE',
                                                                    'doc_count' => 6,
                                                                ),
                                                            7 =>
                                                                array (
                                                                    'key' => 'CONTROLE_CARTAO',
                                                                    'doc_count' => 6,
                                                                ),
                                                            8 =>
                                                                array (
                                                                    'key' => 'CONTROLE_FACIL',
                                                                    'doc_count' => 5,
                                                                ),
                                                            9 =>
                                                                array (
                                                                    'key' => 'OI_CONTROLE_BOLETO',
                                                                    'doc_count' => 5,
                                                                ),
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CLARO_PRE',
                                                                    'doc_count' => 27,
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'VIVO_PRE',
                                                                    'doc_count' => 1,
                                                                ),
                                                        ),
                                                ),
                                        ),
                                    3 =>
                                        array (
                                            'key' => 'Regional REC',
                                            'doc_count' => 143,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CONTROLE',
                                                                    'doc_count' => 30,
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'OI_CONTROLE_CARTAO',
                                                                    'doc_count' => 18,
                                                                ),
                                                            2 =>
                                                                array (
                                                                    'key' => 'CONTROLE_FACIL',
                                                                    'doc_count' => 17,
                                                                ),
                                                            3 =>
                                                                array (
                                                                    'key' => 'TIM_EXPRESS',
                                                                    'doc_count' => 14,
                                                                ),
                                                            4 =>
                                                                array (
                                                                    'key' => 'TIM_CONTROLE_FATURA',
                                                                    'doc_count' => 8,
                                                                ),
                                                            5 =>
                                                                array (
                                                                    'key' => 'CONTROLE_BOLETO',
                                                                    'doc_count' => 7,
                                                                ),
                                                            6 =>
                                                                array (
                                                                    'key' => 'CONTROLE_CARTAO',
                                                                    'doc_count' => 4,
                                                                ),
                                                            7 =>
                                                                array (
                                                                    'key' => 'OI_CONTROLE_BOLETO',
                                                                    'doc_count' => 4,
                                                                ),
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CLARO_PRE',
                                                                    'doc_count' => 40,
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'VIVO_PRE',
                                                                    'doc_count' => 1,
                                                                ),
                                                        ),
                                                ),
                                        ),
                                    4 =>
                                        array (
                                            'key' => 'Regional 5',
                                            'doc_count' => 138,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CONTROLE_BOLETO',
                                                                    'doc_count' => 27,
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'TIM_EXPRESS',
                                                                    'doc_count' => 14,
                                                                ),
                                                            2 =>
                                                                array (
                                                                    'key' => 'CONTROLE_FACIL',
                                                                    'doc_count' => 11,
                                                                ),
                                                            3 =>
                                                                array (
                                                                    'key' => 'OI_CONTROLE_CARTAO',
                                                                    'doc_count' => 10,
                                                                ),
                                                            4 =>
                                                                array (
                                                                    'key' => 'TIM_CONTROLE_FATURA',
                                                                    'doc_count' => 10,
                                                                ),
                                                            5 =>
                                                                array (
                                                                    'key' => 'CONTROLE',
                                                                    'doc_count' => 9,
                                                                ),
                                                            6 =>
                                                                array (
                                                                    'key' => 'OI_CONTROLE_BOLETO',
                                                                    'doc_count' => 8,
                                                                ),
                                                            7 =>
                                                                array (
                                                                    'key' => 'CONTROLE_CARTAO',
                                                                    'doc_count' => 3,
                                                                ),
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CLARO_POS',
                                                                    'doc_count' => 3,
                                                                ),
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array (
                                                            0 =>
                                                                array (
                                                                    'key' => 'CLARO_PRE',
                                                                    'doc_count' => 39,
                                                                ),
                                                            1 =>
                                                                array (
                                                                    'key' => 'VIVO_PRE',
                                                                    'doc_count' => 4,
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
