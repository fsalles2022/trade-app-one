<?php

namespace Reports\Tests\Fixture;

class ElasticSearchTopPointsOfSaleByOperationFixture
{
    public static function getSaleArray()
    {
        return array (
            'took' => 7,
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
                    'total' => 15537,
                    'max_score' => 0,
                    'hits' =>
                        array (
                        ),
                ),
            'aggregations' =>
                array (
                    'POINTS_OF_SALES' =>
                        array (
                            'doc_count_error_upper_bound' => 66,
                            'sum_other_doc_count' => 14606,
                            'buckets' =>
                                array (
                                    0 =>
                                        array (
                                            'key' => '45242914013428',
                                            'doc_count' => 126,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count' => 120,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 4107.490070343017578125,
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 0,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 6,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                        ),
                                    1 =>
                                        array (
                                            'key' => '45242914006480',
                                            'doc_count' => 123,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count' => 104,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 4062.940090179443359375,
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 0,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 19,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                        ),
                                    2 =>
                                        array (
                                            'key' => '45242914006995',
                                            'doc_count' => 105,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count' => 85,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 4201.370059967041015625,
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 0,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 20,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                        ),
                                    3 =>
                                        array (
                                            'key' => '33200056002001',
                                            'doc_count' => 100,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count' => 71,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 3588.3500823974609375,
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 0,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 26,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                        ),
                                    4 =>
                                        array (
                                            'key' => '33200056006180',
                                            'doc_count' => 99,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count' => 88,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 4877.700038909912109375,
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 0,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 11,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                        ),
                                    5 =>
                                        array (
                                            'key' => '33200056001030',
                                            'doc_count' => 97,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count' => 85,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 4513.060085296630859375,
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 0,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 11,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                        ),
                                    6 =>
                                        array (
                                            'key' => '33200056021812',
                                            'doc_count' => 78,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count' => 59,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 2907.140033721923828125,
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 0,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 19,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                        ),
                                    7 =>
                                        array (
                                            'key' => '45242914016281',
                                            'doc_count' => 73,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count' => 57,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 2273.06003570556640625,
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 0,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 16,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                        ),
                                    8 =>
                                        array (
                                            'key' => '33200056022207',
                                            'doc_count' => 66,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count' => 64,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 3573.920032501220703125,
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 0,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 2,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                        ),
                                    9 =>
                                        array (
                                            'key' => '61099834063273',
                                            'doc_count' => 64,
                                            'CONTROLE' =>
                                                array (
                                                    'doc_count' => 31,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 948.29999542236328125,
                                                        ),
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 0,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 33,
                                                    'REVENUES' =>
                                                        array (
                                                            'value' => 0,
                                                        ),
                                                ),
                                        ),
                                ),
                        ),
                ),
        );
    }
}
