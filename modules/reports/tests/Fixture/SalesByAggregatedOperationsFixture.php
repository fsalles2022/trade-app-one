<?php

namespace Reports\Tests\Fixture;

class SalesByAggregatedOperationsFixture
{
    public static function getSaleArray()
    {
        return array(
            'took' => 0,
            'timed_out' => false,
            '_shards' =>
                array(
                    'total' => 5,
                    'successful' => 5,
                    'skipped' => 0,
                    'failed' => 0,
                ),
            'hits' =>
                array(
                    'total' => 16312,
                    'max_score' => 0,
                    'hits' =>
                        array(),
                ),
            'aggregations' =>
                array(
                    'POINT_OF_SALE_DETAIL' =>
                        array(
                            'doc_count_error_upper_bound' => 0,
                            'sum_other_doc_count' => 0,
                            'buckets' =>
                                array(
                                    0 =>
                                        array(
                                            'key' => '33200056002001',
                                            'doc_count' => 8478,
                                            'PRE' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(
                                                            0 =>
                                                                array(
                                                                    'key' => 'CLARO_PRE',
                                                                    'doc_count' => 2003,
                                                                ),
                                                            1 =>
                                                                array(
                                                                    'key' => 'VIVO_PRE',
                                                                    'doc_count' => 53,
                                                                ),
                                                        ),
                                                ),
                                            'CONTROLE' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(
                                                            0 =>
                                                                array(
                                                                    'key' => 'CONTROLE_BOLETO',
                                                                    'doc_count' => 2310,
                                                                ),
                                                            1 =>
                                                                array(
                                                                    'key' => 'CONTROLE_FACIL',
                                                                    'doc_count' => 1458,
                                                                ),
                                                            2 =>
                                                                array(
                                                                    'key' => 'TIM_EXPRESS',
                                                                    'doc_count' => 1022,
                                                                ),
                                                            3 =>
                                                                array(
                                                                    'key' => 'TIM_CONTROLE_FATURA',
                                                                    'doc_count' => 663,
                                                                ),
                                                            4 =>
                                                                array(
                                                                    'key' => 'CONTROLE',
                                                                    'doc_count' => 439,
                                                                ),
                                                            5 =>
                                                                array(
                                                                    'key' => 'OI_CONTROLE_CARTAO',
                                                                    'doc_count' => 286,
                                                                ),
                                                            6 =>
                                                                array(
                                                                    'key' => 'CONTROLE_CARTAO',
                                                                    'doc_count' => 219,
                                                                ),
                                                            7 =>
                                                                array(
                                                                    'key' => 'OI_CONTROLE_BOLETO',
                                                                    'doc_count' => 25,
                                                                ),
                                                        ),
                                                ),
                                            'POS' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(),
                                                ),
                                        ),
                                    1 =>
                                        array(
                                            'key' => '33200056022207',
                                            'doc_count' => 7834,
                                            'PRE' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(
                                                            0 =>
                                                                array(
                                                                    'key' => 'CLARO_PRE',
                                                                    'doc_count' => 401,
                                                                ),
                                                            1 =>
                                                                array(
                                                                    'key' => 'VIVO_PRE',
                                                                    'doc_count' => 7,
                                                                ),
                                                        ),
                                                ),
                                            'CONTROLE' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(
                                                            0 =>
                                                                array(
                                                                    'key' => 'CONTROLE',
                                                                    'doc_count' => 3778,
                                                                ),
                                                            1 =>
                                                                array(
                                                                    'key' => 'CONTROLE_BOLETO',
                                                                    'doc_count' => 1591,
                                                                ),
                                                            2 =>
                                                                array(
                                                                    'key' => 'TIM_CONTROLE_FATURA',
                                                                    'doc_count' => 687,
                                                                ),
                                                            3 =>
                                                                array(
                                                                    'key' => 'CONTROLE_FACIL',
                                                                    'doc_count' => 416,
                                                                ),
                                                            4 =>
                                                                array(
                                                                    'key' => 'TIM_EXPRESS',
                                                                    'doc_count' => 376,
                                                                ),
                                                            5 =>
                                                                array(
                                                                    'key' => 'CONTROLE_CARTAO',
                                                                    'doc_count' => 318,
                                                                ),
                                                            6 =>
                                                                array(
                                                                    'key' => 'OI_CONTROLE_CARTAO',
                                                                    'doc_count' => 199,
                                                                ),
                                                            7 =>
                                                                array(
                                                                    'key' => 'OI_CONTROLE_BOLETO',
                                                                    'doc_count' => 61,
                                                                ),
                                                        ),
                                                ),
                                            'POS' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(),
                                                ),
                                        ),
                                ),
                        ),
                ),
        );
    }
}
