<?php

use TradeAppOne\Domain\Enumerators\NetworkEnum;

return [
    'siv'    => [
        'uri'      => env('SIV_API', ''),
        'token'    => env('SIV_API_TOKEN', ''),
        'sentinel' => env('SIV_API_SENTINEL', ''),
        'headers'  => [
            'Content-Type'          => 'application/json',
            'Accept'                => 'application/json'
        ],
        'headers-by-context' => [
            'x-api-key'             => env('SIV_API_KEY', ''),
            'x-api-key-alternative' => env('SIV_CLARO_PROMOTOR_API_KEY', ''),
        ]
    ],
    'siv3' => [
        'uri' => env('SIV3_API', ''),
        'login' => env('SIV3_API_LOGIN', ''),
        'password' => env('SIV3_API_PASSWORD', ''),
        'headers' => [
            'Content-Type'          => 'application/json',
            'Accept'                => 'application/json'
        ]
    ],
    'tradehub' => [
        'uri' => env('TRADE_HUB_API', ''),
        'key' => env('TRADE_HUB_KEY', ''),
        'secret' => env('TRADE_HUB_SECRET', ''),
        'login' => env('TRADE_HUB_LOGIN', ''),
        'password' => env('TRADE_HUB_PASSWORD', ''),
        'captcha-key' => env('TRADE_HUB_CAPTCHA_KEY', ''),
        'via-varejo-seller-password' => env('TRADE_HUB_VIA_VAREJO_SELLER_PASSWORD', ''),
        'headers' => [
            'Content-Type'          => 'application/json',
            'Accept'                => 'application/json'
        ]
    ],
    'sun'    => [
        'uri'     => env('SUN_API', ''),
        'headers' => [
            'cea'           => [
                'SUN-API-Token' => env('SUN_API_TOKEN_CEA', ''),
            ],
            'riachuelo'     => [
                'SUN-API-Token' => env('SUN_API_TOKEN_RIACHUELO', ''),
            ],
            'herval'        => [
                'SUN-API-Token' => env('SUN_API_TOKEN_HERVAL', ''),
            ],
            'pernambucanas' => [
                'SUN-API-Token' => env('SUN_API_TOKEN_PERNAMBUCANAS', ''),
            ],
            'lebes'         => [
                'SUN-API-Token' => env('SUN_API_TOKEN_LEBES', ''),
            ],
            NetworkEnum::EXTRA         => [
                'SUN-API-Token' => env('SUN_API_TOKEN_EXTRA', ''),
            ],
            NetworkEnum::SCHUMANN      => [
                'SUN-API-Token' => env('SUN_API_TOKEN_SCHUMANN', ''),
            ],
            NetworkEnum::ELETROZEMA    => [
                'SUN-API-Token' => env('SUN_API_TOKEN_ELETROZEMA', ''),
            ],
            NetworkEnum::FUJIOKA       => [
                'SUN-API-Token' => env('SUN_API_TOKEN_FUJIOKA', ''),
            ],
            NetworkEnum::CASAEVIDEO => [
                'SUN-API-Token' => env('SUN_API_TOKEN_CASA_E_VIDEO', ''),
            ],
            NetworkEnum::FAST_SHOP => [
                'SUN-API-Token' => env('SUN_API_TOKEN_FAST_SHOP', '')
            ],
            NetworkEnum::AVENIDA => [
                'SUN-API-Token' => env('SUN_API_TOKEN_AVENIDA', ''),
            ]
        ]
    ],
    'uol' => [
        'uri'      => env('UOL_CURSOS_URI', ''),
        'mail'     => env('UOL_CURSOS_MAIL', ''),
        'password' => env('UOL_CURSOS_PASSWORD', '')
    ],
    'mcafee' => [
        'uri'       => env('MCAFEE_API', ''),
        'partnerId' => env('MCAFEE_PARTNER_ID', '')
    ],
    'timBR'  => [
        'oag-uri'                           => env('TIM_API', ''),
        'oam-uri'                           => env('TIM_API_OAM', ''),
        'wso2-uri'                          => env('TIM_API_WSO2', ''),
        'tim-eligibility-uri'               => env('TIM_API_ELIGIBILITY', ''),
        'tim-pmid-uri'                      => env('TIM_API_PMID', ''),
        'tim-order-uri'                     => env('TIM_API_ORDER', ''),
        'oam-curl'                          => env('TIM_CURL_OAM', ''),
        'tim-pem'                           => env('TIM_PEM', ''),
        'tim-premium-commissioning-uri'     => env('TIM_PREMIUM_COMMISSIONING_API', ''),
        'tim-premium-retail-sales-per-page' => env('TIM_PREMIUM_RETAIL_SALES_PER_PAGE', ''),
        'express'                           => env('TIM_EXPRESS_API'),
        'express-key'                       => env('TIM_EXPRESS_KEY'),
        'brscan'  => [
            'uri'                                               => env('TIM_BRSCAN_API', ''),
            'generate-authenticate-api-user'                    => env('TIM_BRSCAN_GENERATE_AUTHENTICATE_API_USER', ''),
            'generate-authenticate-api-password'                => env('TIM_BRSCAN_GENERATE_AUTHENTICATE_API_PASSWORD', ''),
            'authenticate-status-api-user'                      => env('TIM_BRSCAN_AUTHENTICATE_STATUS_API_USER', ''),
            'authenticate-status-api-password'                  => env('TIM_BRSCAN_AUTHENTICATE_STATUS_API_PASSWORD', ''),
            'generate-sale-term-for-signature-api-user'         => env('TIM_BRSCAN_GENERATE_SALE_TERM_FOR_SIGNATURE_API_USER', ''),
            'generate-sale-term-for-signature-api-password'     => env('TIM_BRSCAN_GENERATE_SALE_TERM_FOR_SIGNATURE_API_PASSWORD', ''),
            'sale-term-status-api-user'                         => env('TIM_BRSCAN_SALE_TERM_STATUS_API_USER', ''),
            'sale-term-status-api-password'                     => env('TIM_BRSCAN_SALE_TERM_STATUS_API_PASSWORD', ''),
            'welcome-kit-api-user'                              => env('TIM_BRSCAN_WELCOME_KIT_API_USER', ''),
            'welcome-kit-api-password'                          => env('TIM_BRSCAN_WELCOME_KIT_API_PASSWORD', ''),
        ],
        'eldorado'      => [
            'apiToken' => env('TIM_ELDORADO_KEY', ''),
            'uri'      => env('TIM_ELDORADO_API', ''),
        ],
        'cea'           => [
            'client-id'     => env('TIM_API_CEA_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_CEA_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_CEA_REDIRECT', ''),
            'user-sergeant' => env('TIM_API_CEA_SERGEANT', ''),
        ],
        'iplace'        => [
            'clientId'     => env('TIM_API_IPLACE_CLIENT_ID', ''),
            'clientSecret' => env('TIM_API_IPLACE_CLIENT_SECRET', ''),
        ],
        'riachuelo'     => [
            'client-id'     => env('TIM_API_RIACHUELO_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_RIACHUELO_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_RIACHUELO_REDIRECT', ''),
            'user-sergeant' => env('TIM_API_RIACHUELO_SERGEANT', ''),
        ],
        'pernambucanas' => [
            'client-id'     => env('TIM_API_PERNAMBUCANAS_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_PERNAMBUCANAS_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_PERNAMBUCANAS_REDIRECT', ''),
            'user-sergeant' => env('TIM_API_PERNAMBUCANAS_SERGEANT', ''),
        ],
        'taqi'          => [
            'client-id'     => env('TIM_API_TAQI_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_TAQI_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_TAQI_REDIRECT', ''),
            'user-sergeant' => env('TIM_API_TAQI_SERGEANT', ''),
        ],
        'lebes'         => [
            'client-id'     => env('TIM_API_LEBES_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_LEBES_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_LEBES_REDIRECT', ''),
        ],
        'extra'         => [
            'client-id'     => env('TIM_API_EXTRA_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_EXTRA_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_EXTRA_REDIRECT', ''),
        ],
        'schumann'         => [
            'client-id'     => env('TIM_API_SCHUMANN_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_SCHUMANN_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_SCHUMANN_REDIRECT', ''),
        ],
        'fujioka' => [
            'client-id'     => env('TIM_API_FUJIOKA_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_FUJIOKA_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_FUJIOKA_REDIRECT', ''),
        ],
        'eletrozema' => [
            'client-id'     => env('TIM_API_ELETROZEMA_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_ELETROZEMA_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_ELETROZEMA_REDIRECT', ''),
        ],
        'vertex' => [
            'client-id'     => env('TIM_API_VERTEX_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_VERTEX_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_VERTEX_REDIRECT', ''),
        ],
        'casaevideo' => [
            'client-id'     => env('TIM_API_CASAEVIDEO_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_CASAEVIDEO_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_CASAEVIDEO_REDIRECT', ''),
        ],
        NetworkEnum::IBYTE => [
            'client-id'     => env('TIM_API_IBYTE_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_IBYTE_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_IBYTE_REDIRECT', ''),
        ],
        NetworkEnum::VIA_VAREJO => [
            'client-id'     => env('TIM_API_VIA_VAREJO_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_VIA_VAREJO_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_VIA_VAREJO_REDIRECT', ''),
            'user-sergeant' => env('TIM_API_VIA_VAREJO_SERGEANT', ''),
        ],
        NetworkEnum::AVENIDA => [
            'client-id'     => env('TIM_API_AVENIDA_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_AVENIDA_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_AVENIDA_REDIRECT', ''),
            'user-sergeant' => env('TIM_API_AVENIDA_SERGEANT', ''),
        ],
        NetworkEnum::COLOMBO => [
            'client-id'     => env('TIM_API_COLOMBO_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_COLOMBO_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_COLOMBO_REDIRECT', ''),
        ],
        NetworkEnum::MULTISOM => [
            'client-id'     => env('TIM_API_MULTISOM_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_MULTISOM_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_MULTISOM_REDIRECT', ''),
            'user-sergeant' => env('TIM_API_MULTISOM_SERGEANT', ''),
        ],
        NetworkEnum::LOJAS_TORRA => [
            'client-id'     => env('TIM_API_TORRA_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_TORRA_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_TORRA_REDIRECT', ''),
        ],
        NetworkEnum::LE_BISCUIT => [
            'client-id'     => env('TIM_API_LE_BISCUIT_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_LE_BISCUIT_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_LE_BISCUIT_REDIRECT', ''),
        ],
        NetworkEnum::MERCADO_MOVEIS => [
            'client-id'     => env('TIM_API_MERCADO_MOVEIS_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_MERCADO_MOVEIS_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_MERCADO_MOVEIS_REDIRECT', ''),
        ],
        NetworkEnum::NOVO_MUNDO => [
            'client-id'     => env('TIM_API_NOVO_MUNDO_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_NOVO_MUNDO_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_NOVO_MUNDO_REDIRECT', ''),
        ],
        NetworkEnum::MAGAZAN => [
            'client-id'     => env('TIM_API_MAGAZAN_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_MAGAZAN_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_MAGAZAN_REDIRECT', ''),
        ],
        NetworkEnum::IPLACE => [
            'client-id'     => env('TIM_API_IPLACE_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_IPLACE_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_IPLACE_REDIRECT', ''),
            'user-sergeant' => env('TIM_API_IPLACE_SERGEANT', ''),
        ],
        NetworkEnum::SAMSUNG => [
            'client-id'     => env('TIM_API_SAMSUNG_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_SAMSUNG_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_SAMSUNG_REDIRECT', ''),
        ],
        NetworkEnum::SAMSUNG_MRF => [
            'client-id'     => env('TIM_API_SAMSUNG_MRF_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_SAMSUNG_MRF_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_SAMSUNG_MRF_REDIRECT', ''),
        ],
        NetworkEnum::MASTERCELL => [
            'client-id'     => env('TIM_API_MASTERCELL_CLIENT_ID', ''),
            'client-secret' => env('TIM_API_MASTERCELL_CLIENT_SECRET', ''),
            'redirect-uri'  => env('TIM_API_MASTERCELL_REDIRECT', ''),
        ],
    ],
    'gateway' => [
        'client-id'     => env('PAYMENT_GATEWAY_ID', ''),
        'client-secret' => env('PAYMENT_GATEWAY_ACCESS_KEY', '')
    ],
    'oiBR'   => [
        'uri'      => env('OI_API', ''),
        'apiToken' => env('OI_API_TOKEN', ''),
        'eldorado' => [
            'apiToken' => env('OI_API_TOKEN', ''),
            'uri'      => env('ELDORADO_API', ''),
            'service'  => env('ELDORADO_API_SERVICE', ''),
            'password' => env('ELDORADO_API_PASSWORD', ''),
        ],
        'oiSaleFlow' => env('OI_SALE_FLOW', ''),
        'documentCertification' => env('DOCUMENT_CERTIFICATION', ''),
    ],
    'mapfre' => [],
    'movile' => [
        'uri'             => env('MOVILLE_API', ''),
        'secret'          => env('MOVILLE_SECRET', ''),
        'credential'      => env('MOVILLE_CREDENTIAL', ''),
        'application-id'  => env('MOVILLE_APPLICATION_ID', ''),
        'application-sku' => env('MOVILLE_APPLICATION_SKU', ''),
    ],
    'nextel' => [
        'uri'     => env('NEXTEL_API', ''),
        'api-key' => env('NEXTEL_APIKEY', ''),
        'channel' => env(''),
        'modal'   => [
            'uri'       => env('NEXTEL_MODAL_API', ''),
            'api-key'   => env('NEXTEL_MODAL_APIKEY', ''),
            'modal-uri' => env('NEXTEL_MODAL'),
        ]
    ],
    'vertex' => [
        'uri'     => env('VERTEX_API', ''),
        'token'   => env('VERTEX_API_TOKEN', ''),
        'headers' => [
            'x-api-key'    => env('VERTEX_API_KEY', ''),
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json'
        ]
    ],
    'fastshop'    => [
        'uri'           => env('FASTSHOP_API', ''),
        'client_id'     => env('FASTSHOP_API_CLIENT_ID', ''),
        'client_secret' => env('FASTSHOP_API_CLIENT_SECRET', ''),
        'grant_type'    => env('FASTSHOP_API_GRANT_TYPE', ''),
        'headers'       => [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json'
        ]
    ],
    'generali' => [
        'uri'      => env('GENERALI_API', ''),
        'email'    => env('GENERALI_MAIL_USER', ''),
        'senha'    => env('GENERALI_MAIL_PASSWORD', ''),
        'headers'  => [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json'
        ]
    ],
    'viaVarejo' => [
        'uri'         => env('VIAVAREJO_AUTH_API', ''),
        'scope'       => env('VIAVAREJO_SCOPE', ''),
        'grant_type'  => env('VIAVAREJO_GRANT_TYPE', ''),
        'canal_venda' => env('VIAVAREJO_CANAL_VENDA', ''),
        'username'    => env('VIAVAREJO_USERNAME', ''),
        'password'    => env('VIAVAREJO_PASSWORD', ''),
        'key'         => env('VIAVAREJO_KEY', ''),
        'headers'     => [
            'Accept' => 'application/json'
        ]
    ],
    'pagtel' => [
        'pernambucanas' => [
            'uri'         => env('PAGTEL_PERNAMBUCANAS_API', ''),
            'login'       => env('PAGTEL_PERNAMBUCANAS_LOGIN', ''),
            'password'    => env('PAGTEL_PERNAMBUCANAS_PASSWORD', ''),
            'grant_type'  => env('PAGTEL_PERNAMBUCANAS_GRANT_TYPE', ''),
            'identify'    => \TradeAppOne\Domain\Enumerators\Operations::SURF_PERNAMBUCANAS,
            'headers'     => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ],
        'correios' => [
            'uri'         => env('PAGTEL_CORREIOS_API', ''),
            'login'       => env('PAGTEL_CORREIOS_LOGIN', ''),
            'password'    => env('PAGTEL_CORREIOS_PASSWORD', ''),
            'grant_type'  => env('PAGTEL_CORREIOS_GRANT_TYPE', ''),
            'identify'    => \TradeAppOne\Domain\Enumerators\Operations::SURF_CORREIOS,
            'headers'     => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ]
    ],
];
