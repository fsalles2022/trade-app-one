<?php

namespace Discount\Http\Resources;

use Discount\Models\Discount;
use Discount\Models\DiscountProduct;
use Illuminate\Support\Collection;

class TriangulationSimulationResource
{
    public static function toArray(Collection $collection, int $deviceId)
    {
         $resume = collect();

         $collection->each(function (Discount $triangulation) use ($resume, $deviceId) {
            $device = $triangulation->devices->firstWhere('device.id', '=', $deviceId);
            $triangulation->products->each(function (DiscountProduct $product) use ($triangulation, $device, $resume) {
                $resume->push([
                    'operator'  => $product->operator,
                    'operation' => $product->operation,
                    'label'     => $product->label,
                    'discount'  => [
                        'title'     => $triangulation->title,
                        'startAt'   => $triangulation->startAt,
                        'endAt'     => $triangulation->endAt,
                        'price'     => $device->price,
                        'discount'  => $device->discount,
                        'priceWith' => number_format($device->price - $device->discount, 2, '.', '')
                    ]
                ]);
            });
         });

         return $resume;
    }
}
