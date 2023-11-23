<?php

namespace Discount\Http\Resources;

use Discount\Models\DiscountProduct;
use Illuminate\Support\Collection;

class DiscountInSaleResource
{
    /** @param mixed[] $filters */
    public static function toArray(Collection $collection, bool $setDevice, bool $hasIntegration, array $filters = [])
    {
        $triangulations = collect();
        $operations     = (array) data_get($filters, 'operations');
        $operator       = data_get($filters, 'operator');

        $collection->each(function ($discount) use ($triangulations, $operations, $operator) {
            $discount->devices->each(function ($deviceDiscount) use ($discount, $triangulations, $operations, $operator) {
                $products = $discount->products
                    ->filter(function (DiscountProduct $prod) use ($operations, $operator) {
                        if (empty($operations) || empty($operator)) {
                            return true;
                        }

                        return $prod->operator === $operator && in_array($prod->operation, $operations);
                    })
                    ->values()
                    ->map(function ($prod) use ($discount, $deviceDiscount) {
                        return array_merge($prod->toArray(), [
                            'title'    => $discount->title,
                            'id'       => $discount->id,
                            'price'    => $deviceDiscount->price,
                            'discount' => $deviceDiscount->discount
                        ]);
                    });
                $triangulations->push([
                    'id'       => $deviceDiscount->device->id,
                    'label'    => $deviceDiscount->device->label,
                    'sku'      => $deviceDiscount->device->sku,
                    'model'    => $deviceDiscount->device->model,
                    'discount' => [
                        'title'    => $discount->title,
                        'id'       => $discount->id,
                        'price'    => $deviceDiscount->price,
                        'discount' => $deviceDiscount->discount,
                        'products' => $products
                    ],
                    'products' => $products
                ]);
            });
        });

        return [
            "setDevice"      => $setDevice,
            "hasIntegration" => $hasIntegration,
            "triangulations" => $triangulations->values()
        ];
    }
}
