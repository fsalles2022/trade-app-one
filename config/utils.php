<?php

use TradeAppOne\Domain\Enumerators\Operations;

return [
    'viaCep'   => [
        'uri' => env('VIA_CEP', ''),
    ],
    'webMania' => [
        'uri'    => env('WEBMANIA_CEP', ''),
        'secret' => env('WEBMANIA_CEP_APP_SECRET'),
        'key'    => env('WEBMANIA_CEP_APP_KEY'),
    ],
    'senderMails' => [
        Operations::OI => [
            'emails' => env('MAIL_SENDING_EMAILS_OI', ''),
            'emailsCC' => env('MAIL_SENDING_EMAILS_OI_CC', ''),
        ],
    ],
    'autentica' => [
        'isDisabled' => env('DISABLED_AUTENTICA', 0)
    ],
    'captcha' => [
        'isEnabled' => env('APP_SALE_CAPTCHA_ENABLED', 0),
    ]
];
