<?php

use TradeAppOne\Domain\Enumerators\NetworkEnum;

return [
    NetworkEnum::INOVA => [
        'method' => env('INOVA_WEBHOOK_METHOD', ''),
        'url' => env('INOVA_WEBHOOK_URL', ''),
        'headers' => [
            'client' => base64_encode(env('INOVA_LOGIN') . ':' . env('INOVA_PASSWORD'))
        ]
    ]
];
