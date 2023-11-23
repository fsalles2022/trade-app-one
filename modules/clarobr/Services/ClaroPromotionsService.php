<?php


namespace ClaroBR\Services;

use ClaroBR\Enumerators\ClaroBRCaches;
use Discount\Repositories\DiscountRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use TradeAppOne\Domain\Models\Tables\User;

class ClaroPromotionsService
{
    public static function updatePromotionsCached():Collection
    {
        $promotions  = collect();
        $sentinel    = config('integrations.siv.sentinel');
        $mockUser    = new User(['cpf'=>$sentinel]);
        $sivProducts = self::sivService()->products(['areaCode' => '11'], $mockUser)->filter(function ($product) {
            return $product->promotion !== null;
        });
        $sivProducts->each(function ($product) use ($promotions) {
            $promotion            = $product->promotion;
            $promotion->operator  = $product->operator;
            $promotion->operation = $product->operation;
            $promotion->product   = $product->product;
            unset($promotion->price, $promotion->mode, $promotion->loyalty, $promotion->penalty, $promotion->needDevice);
            $promotions->push((array) $promotion);
        });
        if (Cache::has(ClaroBRCaches::CLARO_PROMOTIONS)) {
            $promotionsCached = Cache::get(ClaroBRCaches::CLARO_PROMOTIONS);
            $promotionIds     = $promotions->pluck('id');

            $promotionsToSoftDelete = $promotionsCached->whereNotIn('id', $promotionIds);

            self::deviceRepository()->removeAllWithPromotionIds($promotionsToSoftDelete);
        }
        Cache::put(ClaroBRCaches::CLARO_PROMOTIONS, $promotions, now()->addHours(ClaroBRCaches::PROMOTIONS_DUE));
        return $promotions;
    }

    public static function getPromotions(Collection $promotionIds = null) : Collection
    {
        if (Cache::has(ClaroBRCaches::CLARO_PROMOTIONS)) {
            $promotionsCached = Cache::get(ClaroBRCaches::CLARO_PROMOTIONS);
            if ($promotionsCached instanceof Collection) {
                if ($promotionIds === null) {
                    return $promotionsCached;
                }
                return $promotionsCached->whereIn('id', $promotionIds);
            }
        }
        self::updatePromotionsCached();

        return self::getPromotions($promotionIds);
    }

    private static function sivService():SivService
    {
        return resolve(SivService::class);
    }

    private static function deviceRepository()
    {
        return resolve(DiscountRepository::class);
    }
}
