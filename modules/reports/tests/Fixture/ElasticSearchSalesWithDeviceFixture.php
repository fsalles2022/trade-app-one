<?php

namespace Reports\Tests\Fixture;

class ElasticSearchSalesWithDeviceFixture
{
    public static function getSaleArray()
    {
        return [
            "took"         => 1,
            "timed_out"    => false,
            "_shards"      => [
                "total"      => 5,
                "successful" => 5,
                "skipped"    => 0,
                "failed"     => 0
            ],
            "hits"         => [
                "total"     => 3537,
                "max_score" => 0.0,
                "hits"      => []
            ],
            "aggregations" => [
                "operator" => [
                    "doc_count_error_upper_bound" => 0,
                    "sum_other_doc_count"         => 0,
                    "buckets"                     => [
                        0 => [
                            "key"             => "CLARO",
                            "doc_count"       => 1710,
                            "operation" => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    0 => [
                                        "key"       => "CONTROLE_FACIL",
                                        "doc_count" => 957,
                                        "sum_price" => [
                                            "value" => 40580.431594849
                                        ]
                                    ],
                                    1 => [
                                        "key"       => "CONTROLE_BOLETO",
                                        "doc_count" => 703,
                                        "sum_price" => [
                                            "value" => 33091.761039734
                                        ]
                                    ],
                                    2 => [
                                        "key"       => "CLARO_POS",
                                        "doc_count" => 50,
                                        "sum_price" => [
                                            "value" => 8839.5000991821
                                        ]
                                    ]
                                ]
                            ],
                            "sum_price" => [
                                "value" => 82511.692733765
                            ]
                        ],
                        1 => [
                            "key"             => "TIM",
                            "doc_count"       => 1063,
                            "operation" => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    0 => [
                                        "key"       => "TIM_CONTROLE_FATURA",
                                        "doc_count" => 842,
                                        "sum_price" => [
                                            "value" => 42396.591388702
                                        ]
                                    ],
                                    1 => [
                                        "key"       => "TIM_EXPRESS",
                                        "doc_count" => 221,
                                        "sum_price" => [
                                            "value" => 11112.790370941
                                        ]
                                    ]
                                ]
                            ],
                            "sum_price" => [
                                "value" => 53509.381759644
                            ]
                        ],
                        2 => [
                            "key"             => "OI",
                            "doc_count"       => 634,
                            "operation" => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    0 => [
                                        "key"       => "OI_CONTROLE_CARTAO",
                                        "doc_count" => 556,
                                        "sum_price" => [
                                            "value" => 23120.820270538
                                        ]
                                    ],
                                    1 => [
                                        "key"       => "OI_CONTROLE_BOLETO",
                                        "doc_count" => 78,
                                        "sum_price" => [
                                            "value" => 3495.8599891663
                                        ]
                                    ]
                                ]
                            ],
                            "sum_price" => [
                                "value" => 82511.692733765
                            ]
                        ],
                        3 => [
                            "key"             => "VIVO",
                            "doc_count"       => 130,
                            "operation" => [
                                "doc_count_error_upper_bound" => 0,
                                "sum_other_doc_count"         => 0,
                                "buckets"                     => [
                                    0 => [
                                        "key"       => "CONTROLE",
                                        "doc_count" => 118,
                                        "sum_price" => [
                                            "value" => 5398.8201980591
                                        ]
                                    ],
                                    1 => [
                                        "key"       => "CONTROLE_CARTAO",
                                        "doc_count" => 9,
                                        "sum_price" => [
                                            "value" => 359.9100151062
                                        ]
                                    ],
                                    2 => [
                                        "key"       => "VIVO_PRE",
                                        "doc_count" => 3,
                                        "sum_price" => [
                                            "value" => 0.0
                                        ]
                                    ]
                                ]
                            ],
                            "sum_price" => [
                                "value" => 5758.7302131653
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
