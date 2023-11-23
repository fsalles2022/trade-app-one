<?php

declare(strict_types=1);

namespace Outsourced\ViaVarejo\Helpers;

use Illuminate\Support\Facades\Cache;
use TradeAppOne\Domain\Models\Tables\User;

class UserCacheHelper
{
    public static function make(): self
    {
        return new self();
    }

    public function putViaVarejoUserPointOfSaleAlternate(User $user, string $pointOfSaleCode): void
    {
        Cache::put("VIA_VAREJO_USER_{$user->id}_POINT_OF_SALE_ALTERNATE", $pointOfSaleCode, 60);
    }

    public function getViaVarejoUserPointOfSaleAlternateByUserId(int $userId): ?string
    {
        return Cache::get("VIA_VAREJO_USER_{$userId}_POINT_OF_SALE_ALTERNATE");
    }
}
