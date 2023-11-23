<?php

namespace Reports\Tests\Fixture;

class ElasticSearchMonthGroupOfSales
{
    public static function fixture()
    {
        return [
            "took"         => 103,
            "timed_out"    => false,
            "_shards"      => [
                "total"      => 5,
                "successful" => 5,
                "skipped"    => 0,
                "failed"     => 0
            ],
            "hits"         => [
                "total"     => 447490,
                "max_score" => 0,
                "hits"      => []
            ],
            "aggregations" => [
                "operator" => [
                    "doc_count_error_upper_bound" => 0,
                    "sum_other_doc_count"         => 0,
                    "buckets"                     => [
                        [
                            "key"       => "CLARO",
                            "doc_count" => 184960,
                            "PRE_PAGO"  => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    [
                                        "key"       => "CLARO_PRE",
                                        "doc_count" => 32707,
                                        "prices"    => [
                                            "value" => 0
                                        ]
                                    ]
                                ]
                            ],
                            "POS_PAGO"  => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    [
                                        "key"       => "CONTROLE_BOLETO",
                                        "doc_count" => 98237,
                                        "prices"    => [
                                            "value" => 4989091.5879974365
                                        ]
                                    ],
                                    [
                                        "key"       => "CONTROLE_FACIL",
                                        "doc_count" => 47607,
                                        "prices"    => [
                                            "value" => 2154578.38804245
                                        ]
                                    ],
                                    [
                                        "key"       => "CLARO_POS",
                                        "doc_count" => 6409,
                                        "prices"    => [
                                            "value" => 874665.9195480347
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            "key"       => "TIM",
                            "doc_count" => 109243,
                            "PRE_PAGO"  => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => []
                            ],
                            "POS_PAGO"  => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    [
                                        "key"       => "TIM_CONTROLE_FATURA",
                                        "doc_count" => 71744,
                                        "prices"    => [
                                            "value" => 3709303.324344635
                                        ]
                                    ],
                                    [
                                        "key"       => "TIM_EXPRESS",
                                        "doc_count" => 37498,
                                        "prices"    => [
                                            "value" => 1899410.2024230957
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            "key"       => "VIVO",
                            "doc_count" => 104992,
                            "PRE_PAGO"  => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    [
                                        "key"       => "VIVO_PRE",
                                        "doc_count" => 15710,
                                        "prices"    => [
                                            "value" => 0
                                        ]
                                    ]
                                ]
                            ],
                            "POS_PAGO"  => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    [
                                        "key"       => "CONTROLE",
                                        "doc_count" => 64022,
                                        "prices"    => [
                                            "value" => 2115026.4441604614
                                        ]
                                    ],
                                    [
                                        "key"       => "CONTROLE_CARTAO",
                                        "doc_count" => 24751,
                                        "prices"    => [
                                            "value" => 187280.25735092163
                                        ]
                                    ],
                                    [
                                        "key"       => "VIVO_PRE_PAGO",
                                        "doc_count" => 509,
                                        "prices"    => [
                                            "value" => 16112.97989654541
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            "key"       => "OI",
                            "doc_count" => 37845,
                            "PRE_PAGO"  => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => []
                            ],
                            "POS_PAGO"  => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    [
                                        "key"       => "OI_CONTROLE_CARTAO",
                                        "doc_count" => 26896,
                                        "prices"    => [
                                            "value" => 1121955.1449813843
                                        ]
                                    ],
                                    [
                                        "key"       => "OI_CONTROLE_BOLETO",
                                        "doc_count" => 10949,
                                        "prices"    => [
                                            "value" => 486460.40225982666
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            "key"       => "NEXTEL",
                            "doc_count" => 10345,
                            "PRE_PAGO"  => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => []
                            ],
                            "POS_PAGO"  => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    [
                                        "key"       => "NEXTEL_CONTROLE_BOLETO",
                                        "doc_count" => 10045,
                                        "prices"    => [
                                            "value" => 531344.5634803772
                                        ]
                                    ],
                                    [
                                        "key"       => "NEXTEL_CONTROLE_CARTAO",
                                        "doc_count" => 300,
                                        "prices"    => [
                                            "value" => 15272.0004196167
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
