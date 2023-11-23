<?php

use TradeAppOne\Domain\Enumerators\NetworkEnum;

return [
    NetworkEnum::RIACHUELO => [
        'uri' => env('RIACHUELO_API', ''),
        'client_id' => env('RIACHUELO_CLIENT_ID', ''),
        'client_secret' => env('RIACHUELO_CLIENT_SECRET', '')
    ],
    NetworkEnum::CEA => [
        'uri' => env('CEA_GIFT_CARD_API', ''),
        'login' => env('CEA_GIFT_CARD_LOGIN', ''),
        'password' => env('CEA_GIFT_CARD_PASSWORD', ''),
        'uri_triangulation' => env('CEA_CONSULTA_SERIAL_API', '')
    ],
    NetworkEnum::VIA_VAREJO => [
        'uri' => env('VIAVAREJO_AUTH_API', '')
    ],
    NetworkEnum::GPA => [
        'uri' => env('GPA_API'),
        'username' => env('GPA_USERNAME'),
        'password' => env('GPA_PASSWORD'),
        'grant_type' => env('GPA_GRANT_TYPE'),
        'x_api_key' => env('GPA_X_API_KEY')
    ],
    NetworkEnum::PERNAMBUCANAS => [
        'uri' => env('PERNAMBUCANAS_BI_WEBHOOK_API', ''),
        'authorization' => env('PERNAMBUCANAS_BI_WEBHOOK_AUTHORIZATION', ''),
    ],
    NetworkEnum::CASAEVIDEO => [
        'uri' => env('CASAEVIDEO_API'),
    ]
];
