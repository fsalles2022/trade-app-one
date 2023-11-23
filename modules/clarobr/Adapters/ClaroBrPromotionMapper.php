<?php

namespace ClaroBR\Adapters;

use ClaroBR\Models\PromotionsClaro;
use Illuminate\Support\Collection;

class ClaroBrPromotionMapper
{
    public static function map($planAreaCode)
    {
        $promotions = data_get($planAreaCode, 'promotions', []);

        $collectionOfPromotions = new Collection();
        foreach ($promotions as $promotion) {
            $promotionAdapter = new ClaroBrPromotionAdapter($promotion);
            $adapted          = $promotionAdapter->adapt();

            if ($adapted instanceof PromotionsClaro) {
                $collectionOfPromotions->push($adapted);
            }
        }

        return $collectionOfPromotions;
    }
}
