<?php


namespace Generali\Adapters;

use Generali\Enumerators\GeneraliProductsEnumerators;
use Generali\Exceptions\GeneraliExceptions;
use Generali\Models\GeneraliProduct;
use Illuminate\Support\Arr;

class GeneraliAdapterPlan
{
    public static function run(array $products, $options = []): array
    {
        $result = [];

        foreach ($products as $product) {
            foreach ($product as $key => $plans) {
                if (($key === 'plans') && (Arr::get($product, 'slug_produto') === GeneraliProductsEnumerators::GE)) {
                    $product['plans'] = self::setPlans($plans, $options);
                }
            }

            $result[] = $product;
        }

        return $result;
    }

    private static function setPlans(array $plans, $options): array
    {
        $result        = [];
        $productsPrice = GeneraliProduct::RangeValue($options)->first();

        throw_unless($productsPrice, GeneraliExceptions::productNotFound($options));

        foreach ($plans as $plan) {
            if ($months = data_get($plan, 'limite_vigencia')) {
                $validity                    = GeneraliProduct::getValidity($months);
                $plan['valor_premio_bruto']  = $productsPrice->{$validity . GeneraliProduct::PRICE};
                $plan['equipamento_de_para'] = $productsPrice->{$validity . GeneraliProduct::CODE};
                $result[]                    = $plan;
            }
        }

        return $result;
    }
}
