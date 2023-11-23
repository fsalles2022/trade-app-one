<?php

namespace Reports\Tests\Fixture;

class ElasticSearchSalesByNetworkPlansTelecommunicationFixture
{

    public static function getSaleArray()
    {
        return [
            'took' => 18,
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
                    'total' => 103657,
                    'max_score' => 0,
                    'hits' =>
                        array (
                        ),
                ),
                'aggregations' =>
                array (
                    'networks' =>
                        array (
                            'doc_count_error_upper_bound' => 0,
                            'sum_other_doc_count' => 0,
                            'buckets' =>
                                array (
                                    0 =>
                                        array (
                                            'key' => 'Riachuelo',
                                            'doc_count' => 46611,
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 8065,
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 38546,
                                                ),
                                        ),
                                    1 =>
                                        array (
                                            'key' => 'Pernambucanas',
                                            'doc_count' => 25252,
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 6300,
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 18952,
                                                ),
                                        ),
                                    2 =>
                                        array (
                                            'key' => 'CEA MODAS LTDA',
                                            'doc_count' => 11167,
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 2571,
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 8584,
                                                ),
                                        ),
                                    3 =>
                                        array (
                                            'key' => 'Lebes',
                                            'doc_count' => 1250,
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 598,
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 652,
                                                ),
                                        ),
                                    4 =>
                                        array (
                                            'key' => 'Taqi',
                                            'doc_count' => 706,
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 133,
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 573,
                                                ),
                                        ),
                                    5 =>
                                        array (
                                            'key' => 'Iplace',
                                            'doc_count' => 467,
                                            'PRE_PAGO' =>
                                                array (
                                                    'doc_count' => 2,
                                                ),
                                            'POS_PAGO' =>
                                                array (
                                                    'doc_count' => 465,
                                                ),
                                        ),
                                ),
                        ),
                ),
        ];
    }
}
