<?php

namespace TradeAppOne\Facades;

use Illuminate\Support\Facades\Facade;
use TradeAppOne\Domain\Logging\HeimdallInbound as Inbound;

/**
 * @method static void getElasticHost(): string
 * @method static void getElasticPort(): string
 * @method static void getElasticIndex(): string
 *
 *
 * @method static search()
 * @method static index($request, $response)
 * @method static ping(array $params): bool
 * @method static deleteIndex()
 * @method safeDeleteIndex()
 *
 * @see Inbound
 */
class HeimdallInbound extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Inbound::class;
    }
}
