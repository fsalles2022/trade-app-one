<?php

namespace Core\WebHook\Adapters;

use Illuminate\Support\Arr;
use TradeAppOne\Domain\Models\Collections\Service;

class WebHookServiceAdapter
{
    protected static $hidden = [
        '_id',
        'log',
        'card',
        'license',
        'payment',
        'pointOfSale.network',
        'pointOfSale.hierarchy',
        'operatorIdentifiers',
        'device.products'
    ];

    public static function map(Service $service): array
    {
        $sale    = $service->sale;
        $payload = $service->toArray();

        $payload['salesman']    = $sale->user;
        $payload['pointOfSale'] = $sale->pointOfSale;

        Arr::forget($payload, self::$hidden);

        return $payload;
    }
}
