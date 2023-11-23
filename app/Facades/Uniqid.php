<?php

namespace TradeAppOne\Facades;

use Illuminate\Support\Facades\Facade;
use TradeAppOne\Domain\Enumerators\Facades;

/**
 * @method static generate(): string
 *
 * @see UniqidGenerator
 */
class Uniqid extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Facades::UNIQID;
    }
}
