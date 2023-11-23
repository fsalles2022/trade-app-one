<?php

namespace Discount\Repositories;

use Discount\Models\DiscountProduct;

class ProductRepository
{
    public function create(array $attributes): DiscountProduct
    {
        $instance = new DiscountProduct();
        $instance->fill($attributes)->save();

        return $instance;
    }

    public function delete(array $values, $key = 'id'): bool
    {
        return DiscountProduct::query()->whereIn($key, $values)->delete();
    }
}
