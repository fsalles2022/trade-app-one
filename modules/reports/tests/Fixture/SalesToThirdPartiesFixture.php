<?php

namespace Reports\Tests\Fixture;

class SalesToThirdPartiesFixture
{
    public static function fixture(): array
    {
        return [
            'took' => 381,
            'timed_out' => false,
            '_shards' => [
                'total'      => 5,
                'successful' => 5,
                'skipped'    => 0,
                'failed'     => 0
            ],
            'hits' => [
                'total' => 1716836,
                'max_score' => 0.0,
                'hits' => []
            ],
            'aggregations' => [
                'pointsOfSale' => [
                    'doc_count_error_upper_bound',
                    'sum_other_doc_count',
                    'buckets' => [
                        [
                            'key' => '33200056002001',
                            'doc_count' => 12766,
                            'users' => [
                                'doc_count_error_upper_bound' => 0,
                                'sum_other_doc_count' => 132,
                                'buckets' => [
                                    [
                                        'key'       => '08906449429',
                                        'doc_count' => 2202,
                                        'operators' => [
                                            'doc_count_error_upper_bound' => 0,
                                            'sum_other_doc_count'         => 0,
                                            'buckets' => [
                                                [
                                                    'key' => 'CLARO',
                                                    'doc_count' => 1591,
                                                    'CONTROLE' => [
                                                        'doc_count' => 758
                                                    ],
                                                    'POS_PAGO' => [
                                                        'doc_count' => 0
                                                    ],
                                                    'PRE_PAGO' => [
                                                      'doc_count' => 833
                                                    ]
                                                ],
                                                [
                                                    'key' => 'TIM',
                                                    'doc_count' => 353,
                                                    'CONTROLE' => [
                                                        'doc_count' => 353
                                                    ],
                                                    'POS_PAGO' => [
                                                        'doc_count' => 0
                                                    ],
                                                    'PRE_PAGO' => [
                                                        'doc_count' => 0
                                                    ]
                                                ],
                                                [
                                                    'key' => 'VIVO',
                                                    'doc_count' => 152,
                                                    'CONTROLE' => [
                                                        'doc_count' => 140
                                                    ],
                                                    'POS_PAGO' => [
                                                        'doc_count' => 0
                                                    ],
                                                    'PRE_PAGO' => [
                                                        'doc_count' => 12
                                                    ]
                                                ],
                                                [
                                                    'key' => 'OI',
                                                    'doc_count' => 106,
                                                    'CONTROLE' => [
                                                        'doc_count' => 106
                                                    ],
                                                    'POS_PAGO' => [
                                                        'doc_count' => 0
                                                    ],
                                                    'PRE_PAGO' => [
                                                        'doc_count' => 0
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
