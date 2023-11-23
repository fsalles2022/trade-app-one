<?php

namespace Uol\Services;

use TradeAppOne\Domain\Services\MountNewAttributesService;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;
use Uol\Enumerators\UolPlansEnum;
use Uol\Exceptions\UolExceptions;

class MountNewAttributesFromUol implements MountNewAttributesService
{
    public function getAttributes(array $service): array
    {
        $product          = array_get($service, 'product');
        $planNotAvailable = empty(in_array($product, UolPlansEnum::PLANS_AVAILABLE));

        throw_if($planNotAvailable, new ProductNotFoundException());

        $price = array_get(UolPlansEnum::PRICES, $product);
        throw_if(empty($price), UolExceptions::priceNotFound());

        $label        = array_get(UolPlansEnum::LABEL, $product);
        $passportType = $product;
        return compact('price', 'passportType', 'label');
    }
}
