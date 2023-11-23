<?php

namespace Discount\Adapters;

use Carbon\Carbon;
use ClaroBR\Services\ClaroPromotionsService;
use Discount\Models\Discount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Service;

class DiscountAdapter
{
    public static function adapt(Discount $discount): Collection
    {
        $discount->load('pointsOfSale:label,cnpj,networkId', 'devices', 'user', 'products', 'network');

        $extra = [];
        if (Auth::user()->getNetwork()->slug ?? null === NetworkEnum::VIA_VAREJO) {
            $discount->load('viaVarejoCoupons');
            $extra = self::mountExtraToViaVarejo($discount);
        }

        $createdAt = data_get($discount, 'createdAt');
        $updatedAt = data_get($discount, 'updatedAt');

        return collect([
            'discount' => collect([
                'id'       => data_get($discount, 'id'),
                'status'   => data_get($discount, 'status'),
                'title'    => data_get($discount, 'title'),
                'startAt'  => data_get($discount, 'startAt'),
                'createdAt'=> $createdAt !== null ? Carbon::parse($createdAt)->toDateTimeString() : null,
                'updatedAt'=> $updatedAt !== null ? Carbon::parse($updatedAt)->toDateTimeString() : null,
                'endAt'    => data_get($discount, 'endAt'),
                'operator' => collect(
                    $discount->products->map(static function ($product) {
                        $operator = data_get($product, 'operator');
                        return [
                        'id'    => $operator,
                        'label' => ucwords(strtolower($operator))
                        ];
                    })->unique('id')->values()
                ),
                'operation' => collect(
                    $discount->products->map(static function ($product) {
                        $operation = data_get($product, 'operation');
                        $label     = Service::query()
                            ->where('operator', '=', $product->operator)
                            ->where('operation', '=', $operation)
                            ->get()->pluck('label')->first();
                        return [
                            'operator' => $product->operator,
                            'id' => $operation,
                            'label' => $label ?? trans("operations." . $operation . ".label")
                        ];
                    })->unique()->values()
                ),
                'productFilterMode' => ($discount->products->first() !== null) ? $discount->products->first()->filterMode : null,
                'products' => self::getPromotions($discount),
                'promotions' => ClaroPromotionsService::getPromotions(collect(array_pluck($discount->products->toArray(), 'promotion'))->filter()->unique())->values(),
                'devices' => self::getDevices($discount),
                'filterMode'   => data_get($discount, 'filterMode'),
                'pointsOfSale' => $discount->pointsOfSale->map(function (PointOfSale $pointOfSale) {
                    return [
                        'id'    => $pointOfSale->cnpj,
                        'label' => $pointOfSale->label,
                        'network' => $pointOfSale->networkId,
                    ];
                }),
                'network' => $discount->network->toArray(),
                'user'    => $discount->user->toArray(),
                'extra'   => $extra
            ])
        ]);
    }

    private static function getPromotions(Discount $discount):Collection
    {
        return collect(
            $discount->products->unique(static function ($product) {
                return $product->product.$product->operator.$product->operation;
            })->map(static function ($product) use ($discount) {
                if ($product->label) {
                    $prodFinal = [
                        'operator'  => data_get($product, 'operator'),
                        'operation' => data_get($product, 'operation'),
                        'id'        => data_get($product, 'product'),
                        'label'     => ucwords(Str::lower($product->label))
                    ];
                    if (data_get($product, 'promotion')) {
                        $prodFinal['promotion'] = data_get($product, 'promotion');
                    }
                    return collect($prodFinal);
                }
                return null;
            })->filter()->values()
        );
    }

    private static function getDevices(Discount $discount)
    {
        return collect(
            $discount->devices->groupBy(static function ($item) {
                return (int) (data_get($item, 'discount') * 100);
            })->map(function ($devices) {
                return[
                    'brands' => array_values($devices->groupBy('device.brand')->map(function ($device, $model) {
                        return [
                            'id'    =>$model,
                            'label' =>ucwords(strtolower($model))
                        ];
                    })->all()),
                    'models' => $devices->map(function ($device) {
                        return[
                            'id'    => data_get($device, 'device.id'),
                            'brand' => data_get($device, 'device.brand'),
                            'label' => data_get($device, 'device.label'),
                            'price' => data_get($device, 'device.price', 0),
                            'sku' => data_get($device, 'device.sku')
                        ];
                    }),
                    'discount' => data_get($devices, '0.discount'),
                ];
            })->values()
        );
    }

    private static function mountExtraToViaVarejo(Discount $discount): array
    {
        $coupon = $discount->viaVarejoCoupons()->first();
        if ($coupon) {
            $coupon = $coupon->toArray();
            return [
                'coupon' => data_get($coupon, 'coupon'),
                'campaign' => data_get($coupon, 'campaign'),
                'brand' => str_contains(data_get($coupon, 'coupon'), 'PF') ? 'PF' : 'CB'
            ];
        }
        return [];
    }
}
