<?php

namespace TimBR\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TimMappedProduct
{
    public static function getPrice(Collection $mappedProducts, array $service)
    {
        try {
            if ($loyalty = data_get($service, 'loyalty.id')) {
                $plan = $mappedProducts->where('loyalty.id', $loyalty)->where('product', $service['product'])->first();
            } else {
                $plan = $mappedProducts->where('product', $service['product'])->where('loyalty', null)->first();
            }
            return $plan['price'];
        } catch (\ErrorException $exception) {
            Log::alert('tim-price-' . $exception->getMessage(), ['service' => $service]);
            return data_get($service, 'price');
        }
    }
}
