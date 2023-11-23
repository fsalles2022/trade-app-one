<?php

use Authorization\Constants\ThirdPartiesConstant;

return [
    ThirdPartiesConstant::RIACHUELO => [
        "accessKey"       => env('ACCESS_KEY_RIACHUELO', ''),
        "accessUser"      => env('ACCESS_USER_RIACHUELO', ''),
        "accessWhiteList" => env('ACCESS_WHITELIST_RIACHUELO', '127.0.0.1,localhost'),
        'routes'          => [
            'sales'             => ['GET'],
            'discounts'         => ['GET'],
            'buyback/devices'   => ['GET'],
            'buyback/questions' => ['GET'],
            'buyback/price'     => ['POST'],
        ]
    ],

    ThirdPartiesConstant::IPLACE => [
        "accessKey"       => env('ACCESS_KEY_IPLACE', ''),
        "accessUser"      => env('ACCESS_USER_IPLACE', ''),
        "accessWhiteList" => env('ACCESS_WHITELIST_IPLACE', '127.0.0.1,localhost'),
        'routes'          => []
    ],

    ThirdPartiesConstant::CEA => [
        "accessKey"       => env('ACCESS_KEY_CEA', ''),
        "accessUser"      => env('ACCESS_USER_CEA', ''),
        "accessWhiteList" => env('ACCESS_WHITELIST_CEA', '127.0.0.1,localhost'),
        'routes'          => [
            'sales/list'                                => ['POST'],
            'reports/aggregated/by-group-of-operations' => ['POST'],
        ]
    ],

    ThirdPartiesConstant::LEBES => [
        "accessKey"       => env('ACCESS_KEY_LEBES', ''),
        "accessUser"      => env('ACCESS_USER_LEBES', ''),
        "accessWhiteList" => env('ACCESS_WHITELIST_LEBES', '127.0.0.1,localhost'),
        'routes'          => [
            'sales/list' => ['POST'],
        ]
    ],

    ThirdPartiesConstant::SCHUMANN => [
        "accessKey"       => env('ACCESS_KEY_SCHUMANN', ''),
        "accessUser"      => env('ACCESS_USER_SCHUMANN', ''),
        "accessWhiteList" => env('ACCESS_WHITELIST_SCHUMANN', '127.0.0.1,localhost'),
        'routes'          => [
            'users/create'             => ['POST'],
            'users/list'               => ['POST'],
            'roles'                    => ['GET'],
            '/users\/edit\/[0-9]{11}/' => ['PUT'],
            'users/disable'            => ['GET'],
            'users/enable'             => ['GET'],
        ]
    ],

    ThirdPartiesConstant::CASAEVIDEO => [
        "accessKey"       => env('ACCESS_KEY_CASAEVIDEO', ''),
        "accessUser"      => env('ACCESS_USER_CASAEVIDEO', ''),
        "accessWhiteList" => env('ACCESS_WHITELIST_CASAEVIDEO', '127.0.0.1,localhost'),
        'routes'          => [
            'discounts'                => ['GET'],
            'triangulations/list'      => ['POST'],
            'users/list'               => ['POST'],
            'users/create'             => ['POST'],
            '/users\/edit\/[0-9]{11}/' => ['PUT'],
        ]
    ],

    ThirdPartiesConstant::RICARDO_ELETRO => [
        "accessKey"       => env('ACCESS_KEY_RICARDO_ELETRO', ''),
        "accessUser"      => env('ACCESS_USER_RICARDO_ELETRO', ''),
        "accessWhiteList" => env('ACCESS_WHITELIST_RICARDO_ELETRO', '127.0.0.1,localhost'),
        "routes"          => [
            'sales/list' => ['POST'],
        ]
    ],

    ThirdPartiesConstant::FUJIOKA => [
        "accessKey"       => env('ACCESS_KEY_FUJIOKA', ''),
        "accessUser"      => env('ACCESS_USER_FUJIOKA', ''),
        "accessWhiteList" => env('ACCESS_WHITELIST_FUJIOKA', '127.0.0.1,localhost'),
        "routes"          => [
            'sales/list' => ['POST'],
            'discounts'                => ['GET'],
            'triangulations/list'      => ['POST'],
        ]
    ],

    ThirdPartiesConstant::INOVA => [
        "accessKey"       => env('ACCESS_KEY_INOVA', ''),
        "accessUser"      => env('ACCESS_USER_INOVA', ''),
        "accessWhiteList" => env('ACCESS_WHITELIST_INOVA', '127.0.0.1,localhost'),
        "routes"          => [
            'management/user/personify' => ['POST'],
        ]
    ],
    ThirdPartiesConstant::SIV => [
        "accessKey"       => env('ACCESS_KEY_SIV', ''),
        "accessUser"      => env('ACCESS_USER_SIV', ''),
        "accessWhiteList" => env('ACCESS_WHITELIST_SIV', '*'),
        "routes"          => [
            'points_of_sale/integration' => ['PUT']
        ]
    ]
];


