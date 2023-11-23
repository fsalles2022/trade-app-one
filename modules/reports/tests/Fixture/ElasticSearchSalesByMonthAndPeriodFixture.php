<?php

namespace Reports\Tests\Fixture;

class ElasticSearchSalesByMonthAndPeriodFixture
{
    const CNPJ_SALES_MONTH_AND_PERIOD = '33200056001030';

    public static function getSaleArray()
    {
        return json_decode('{
  "took": 16,
  "timed_out": false,
  "_shards": {
    "total": 5,
    "successful": 5,
    "skipped": 0,
    "failed": 0
  },
  "hits": {
    "total": 4182,
    "max_score": 0,
    "hits": []
  },
  "aggregations": {
    "CONSOLIDATE_OPERATORS_DAY": {
      "buckets": [
        {
          "key": "2019-03-19-*",
          "from": 1552964400000,
          "from_as_string": "2019-03-19",
          "doc_count": 10,
          "CONSOLIDATE_OPERATORS": {
            "doc_count_error_upper_bound": 0,
            "sum_other_doc_count": 0,
            "buckets": [
              {
                "key": "CLARO",
                "doc_count": 7,
                "CONSOLIDATE_OPERATIONS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "CLARO_PRE",
                      "doc_count": 3,
                      "prices": {
                        "value": 0
                      }
                    },
                    {
                      "key": "CONTROLE_BOLETO",
                      "doc_count": 2,
                      "prices": {
                        "value": 119.9800033569336
                      }
                    },
                    {
                      "key": "CONTROLE_FACIL",
                      "doc_count": 2,
                      "prices": {
                        "value": 109.9800033569336
                      }
                    }
                  ]
                }
              },
              {
                "key": "OI",
                "doc_count": 2,
                "CONSOLIDATE_OPERATIONS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "OI_CONTROLE_BOLETO",
                      "doc_count": 1,
                      "prices": {
                        "value": 59.900001525878906
                      }
                    },
                    {
                      "key": "OI_CONTROLE_CARTAO",
                      "doc_count": 1,
                      "prices": {
                        "value": 54.869998931884766
                      }
                    }
                  ]
                }
              },
              {
                "key": "VIVO",
                "doc_count": 1,
                "CONSOLIDATE_OPERATIONS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "CONTROLE",
                      "doc_count": 1,
                      "prices": {
                        "value": 49.9900016784668
                      }
                    }
                  ]
                }
              }
            ]
          }
        }
      ]
    },
    "CONSOLIDATE_OPERATORS_DMINUS": {
      "buckets": [
        {
          "key": "2019-03-18 0-0-0-2019-03-18 23-59-59",
          "from": 1552878000000,
          "from_as_string": "2019-03-18 0-0-0",
          "to": 1552964399000,
          "to_as_string": "2019-03-18 23-59-59",
          "doc_count": 15,
          "CONSOLIDATE_OPERATORS": {
            "doc_count_error_upper_bound": 0,
            "sum_other_doc_count": 0,
            "buckets": [
              {
                "key": "CLARO",
                "doc_count": 8,
                "CONSOLIDATE_OPERATIONS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "CONTROLE_BOLETO",
                      "doc_count": 6,
                      "prices": {
                        "value": 329.9400100708008
                      }
                    },
                    {
                      "key": "CLARO_PRE",
                      "doc_count": 1,
                      "prices": {
                        "value": 0
                      }
                    },
                    {
                      "key": "CONTROLE_FACIL",
                      "doc_count": 1,
                      "prices": {
                        "value": 54.9900016784668
                      }
                    }
                  ]
                }
              },
              {
                "key": "VIVO",
                "doc_count": 6,
                "CONSOLIDATE_OPERATIONS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "CONTROLE",
                      "doc_count": 6,
                      "prices": {
                        "value": 299.9400100708008
                      }
                    }
                  ]
                }
              },
              {
                "key": "TIM",
                "doc_count": 1,
                "CONSOLIDATE_OPERATIONS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "TIM_CONTROLE_FATURA",
                      "doc_count": 1,
                      "prices": {
                        "value": 64.98999786376953
                      }
                    }
                  ]
                }
              }
            ]
          }
        }
      ]
    },
    "POINT_OF_SALE_DETAIL": {
      "doc_count_error_upper_bound": 0,
      "sum_other_doc_count": 0,
      "buckets": [
        {
          "key": "' . self::CNPJ_SALES_MONTH_AND_PERIOD .'",
          "doc_count": 4182,
          "CONSOLIDATE_OPERATORS_DAY": {
            "buckets": [
              {
                "key": "2019-03-19-*",
                "from": 1552964400000,
                "from_as_string": "2019-03-19",
                "doc_count": 10,
                "CONSOLIDATE_OPERATORS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "CLARO",
                      "doc_count": 7,
                      "CONSOLIDATE_OPERATIONS": {
                        "doc_count_error_upper_bound": 0,
                        "sum_other_doc_count": 0,
                        "buckets": [
                          {
                            "key": "CLARO_PRE",
                            "doc_count": 3,
                            "prices": {
                              "value": 0
                            }
                          },
                          {
                            "key": "CONTROLE_BOLETO",
                            "doc_count": 2,
                            "prices": {
                              "value": 119.9800033569336
                            }
                          },
                          {
                            "key": "CONTROLE_FACIL",
                            "doc_count": 2,
                            "prices": {
                              "value": 109.9800033569336
                            }
                          }
                        ]
                      }
                    },
                    {
                      "key": "OI",
                      "doc_count": 2,
                      "CONSOLIDATE_OPERATIONS": {
                        "doc_count_error_upper_bound": 0,
                        "sum_other_doc_count": 0,
                        "buckets": [
                          {
                            "key": "OI_CONTROLE_BOLETO",
                            "doc_count": 1,
                            "prices": {
                              "value": 59.900001525878906
                            }
                          },
                          {
                            "key": "OI_CONTROLE_CARTAO",
                            "doc_count": 1,
                            "prices": {
                              "value": 54.869998931884766
                            }
                          }
                        ]
                      }
                    },
                    {
                      "key": "VIVO",
                      "doc_count": 1,
                      "CONSOLIDATE_OPERATIONS": {
                        "doc_count_error_upper_bound": 0,
                        "sum_other_doc_count": 0,
                        "buckets": [
                          {
                            "key": "CONTROLE",
                            "doc_count": 1,
                            "prices": {
                              "value": 49.9900016784668
                            }
                          }
                        ]
                      }
                    }
                  ]
                }
              }
            ]
          },
          "CONSOLIDATE_OPERATORS_DMINUS": {
            "buckets": [
              {
                "key": "2019-03-18 0-0-0-2019-03-18 23-59-59",
                "from": 1552878000000,
                "from_as_string": "2019-03-18 0-0-0",
                "to": 1552964399000,
                "to_as_string": "2019-03-18 23-59-59",
                "doc_count": 15,
                "CONSOLIDATE_OPERATORS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "CLARO",
                      "doc_count": 8,
                      "CONSOLIDATE_OPERATIONS": {
                        "doc_count_error_upper_bound": 0,
                        "sum_other_doc_count": 0,
                        "buckets": [
                          {
                            "key": "CONTROLE_BOLETO",
                            "doc_count": 6,
                            "prices": {
                              "value": 329.9400100708008
                            }
                          },
                          {
                            "key": "CLARO_PRE",
                            "doc_count": 1,
                            "prices": {
                              "value": 0
                            }
                          },
                          {
                            "key": "CONTROLE_FACIL",
                            "doc_count": 1,
                            "prices": {
                              "value": 54.9900016784668
                            }
                          }
                        ]
                      }
                    },
                    {
                      "key": "VIVO",
                      "doc_count": 6,
                      "CONSOLIDATE_OPERATIONS": {
                        "doc_count_error_upper_bound": 0,
                        "sum_other_doc_count": 0,
                        "buckets": [
                          {
                            "key": "CONTROLE",
                            "doc_count": 6,
                            "prices": {
                              "value": 299.9400100708008
                            }
                          }
                        ]
                      }
                    },
                    {
                      "key": "TIM",
                      "doc_count": 1,
                      "CONSOLIDATE_OPERATIONS": {
                        "doc_count_error_upper_bound": 0,
                        "sum_other_doc_count": 0,
                        "buckets": [
                          {
                            "key": "TIM_CONTROLE_FATURA",
                            "doc_count": 1,
                            "prices": {
                              "value": 64.98999786376953
                            }
                          }
                        ]
                      }
                    }
                  ]
                }
              }
            ]
          },
          "CONSOLIDATE_OPERATORS_MONTH": {
            "buckets": [
              {
                "key": "2019-03-01-*",
                "from": 1551409200000,
                "from_as_string": "2019-03-01",
                "doc_count": 554,
                "CONSOLIDATE_OPERATORS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "CLARO",
                      "doc_count": 294,
                      "CONSOLIDATE_OPERATIONS": {
                        "doc_count_error_upper_bound": 0,
                        "sum_other_doc_count": 0,
                        "buckets": [
                          {
                            "key": "CONTROLE_BOLETO",
                            "doc_count": 142,
                            "prices": {
                              "value": 7908.580230712891
                            }
                          },
                          {
                            "key": "CLARO_PRE",
                            "doc_count": 86,
                            "prices": {
                              "value": 0
                            }
                          },
                          {
                            "key": "CONTROLE_FACIL",
                            "doc_count": 66,
                            "prices": {
                              "value": 3509.3401107788086
                            }
                          }
                        ]
                      }
                    },
                    {
                      "key": "VIVO",
                      "doc_count": 136,
                      "CONSOLIDATE_OPERATIONS": {
                        "doc_count_error_upper_bound": 0,
                        "sum_other_doc_count": 0,
                        "buckets": [
                          {
                            "key": "CONTROLE",
                            "doc_count": 134,
                            "prices": {
                              "value": 6713.6602210998535
                            }
                          },
                          {
                            "key": "CONTROLE_CARTAO",
                            "doc_count": 1,
                            "prices": {
                              "value": 39.9900016784668
                            }
                          },
                          {
                            "key": "VIVO_PRE",
                            "doc_count": 1,
                            "prices": {
                              "value": 0
                            }
                          }
                        ]
                      }
                    },
                    {
                      "key": "TIM",
                      "doc_count": 88,
                      "CONSOLIDATE_OPERATIONS": {
                        "doc_count_error_upper_bound": 0,
                        "sum_other_doc_count": 0,
                        "buckets": [
                          {
                            "key": "TIM_EXPRESS",
                            "doc_count": 69,
                            "prices": {
                              "value": 3449.310115814209
                            }
                          },
                          {
                            "key": "TIM_CONTROLE_FATURA",
                            "doc_count": 19,
                            "prices": {
                              "value": 1124.8099937438965
                            }
                          }
                        ]
                      }
                    },
                    {
                      "key": "OI",
                      "doc_count": 36,
                      "CONSOLIDATE_OPERATIONS": {
                        "doc_count_error_upper_bound": 0,
                        "sum_other_doc_count": 0,
                        "buckets": [
                          {
                            "key": "OI_CONTROLE_CARTAO",
                            "doc_count": 25,
                            "prices": {
                              "value": 1312.1899757385254
                            }
                          },
                          {
                            "key": "OI_CONTROLE_BOLETO",
                            "doc_count": 11,
                            "prices": {
                              "value": 658.900016784668
                            }
                          }
                        ]
                      }
                    }
                  ]
                }
              }
            ]
          }
        }
      ]
    },
    "CONSOLIDATE_OPERATORS_MONTH": {
      "buckets": [
        {
          "key": "2019-03-01-*",
          "from": 1551409200000,
          "from_as_string": "2019-03-01",
          "doc_count": 554,
          "CONSOLIDATE_OPERATORS": {
            "doc_count_error_upper_bound": 0,
            "sum_other_doc_count": 0,
            "buckets": [
              {
                "key": "CLARO",
                "doc_count": 294,
                "CONSOLIDATE_OPERATIONS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "CONTROLE_BOLETO",
                      "doc_count": 142,
                      "prices": {
                        "value": 7908.580230712891
                      }
                    },
                    {
                      "key": "CLARO_PRE",
                      "doc_count": 86,
                      "prices": {
                        "value": 0
                      }
                    },
                    {
                      "key": "CONTROLE_FACIL",
                      "doc_count": 66,
                      "prices": {
                        "value": 3509.3401107788086
                      }
                    }
                  ]
                }
              },
              {
                "key": "VIVO",
                "doc_count": 136,
                "CONSOLIDATE_OPERATIONS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "CONTROLE",
                      "doc_count": 134,
                      "prices": {
                        "value": 6713.6602210998535
                      }
                    },
                    {
                      "key": "CONTROLE_CARTAO",
                      "doc_count": 1,
                      "prices": {
                        "value": 39.9900016784668
                      }
                    },
                    {
                      "key": "VIVO_PRE",
                      "doc_count": 1,
                      "prices": {
                        "value": 0
                      }
                    }
                  ]
                }
              },
              {
                "key": "TIM",
                "doc_count": 88,
                "CONSOLIDATE_OPERATIONS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "TIM_EXPRESS",
                      "doc_count": 69,
                      "prices": {
                        "value": 3449.310115814209
                      }
                    },
                    {
                      "key": "TIM_CONTROLE_FATURA",
                      "doc_count": 19,
                      "prices": {
                        "value": 1124.8099937438965
                      }
                    }
                  ]
                }
              },
              {
                "key": "OI",
                "doc_count": 36,
                "CONSOLIDATE_OPERATIONS": {
                  "doc_count_error_upper_bound": 0,
                  "sum_other_doc_count": 0,
                  "buckets": [
                    {
                      "key": "OI_CONTROLE_CARTAO",
                      "doc_count": 25,
                      "prices": {
                        "value": 1312.1899757385254
                      }
                    },
                    {
                      "key": "OI_CONTROLE_BOLETO",
                      "doc_count": 11,
                      "prices": {
                        "value": 658.900016784668
                      }
                    }
                  ]
                }
              }
            ]
          }
        }
      ]
    }
  }
}', true);
    }
}
