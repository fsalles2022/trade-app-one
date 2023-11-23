<?php

namespace TradeAppOne\Facades;

use Illuminate\Support\Facades\Facade;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\SyncUserOperatorsService;

/**
 * @method static sync(User $user, PointOfSale $pointOfSale, array $changes = [])
 * @method static vivo(User $user, PointOfSale $pointOfSale)
 *
 * @see SyncUserOperatorsService
 */
class SyncUserOperators extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SyncUserOperatorsService::class;
    }
}
