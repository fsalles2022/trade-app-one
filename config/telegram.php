<?php

use TradeAppOne\Domain\Enumerators\NetworkEnum;

return [
    'uri' => env('TELEGRAM_BOT_TOKEN', ''),
    'developer' => env('TELEGRAM_DEVELOPER_CHAT_ID', ''),
    NetworkEnum::CEA => env('TELEGRAM_CEA_CHAT_ID', ''),
    NetworkEnum::TAQI => env('TELEGRAM_TAQI_CHAT_ID', ''),
    NetworkEnum::IPLACE => env('TELEGRAM_IPLACE_CHAT_ID', ''),
    NetworkEnum::RIACHUELO => env('TELEGRAM_RIACHUELO_CHAT_ID', ''),
    NetworkEnum::LEBES => env('TELEGRAM_LEBES_CHAT_ID', ''),
    NetworkEnum::PERNAMBUCANAS => env('TELEGRAM_PERNAMBUCANAS_CHAT_ID', ''),
    NetworkEnum::EXTRA => env('TELEGRAM_EXTRA_CHAT_ID', ''),
    NetworkEnum::FUJIOKA => env('TELEGRAM_FUJIOKA_CHAT_ID', ''),
    NetworkEnum::SCHUMANN => env('TELEGRAM_SCHUMANN_CHAT_ID', ''),
    'notify-channel' => env('TELEGRAM_PANELS_NOTIFY_CHAT_ID', '')
];
