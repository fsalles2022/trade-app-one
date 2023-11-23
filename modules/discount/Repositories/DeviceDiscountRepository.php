<?php

namespace Discount\Repositories;

use Discount\Models\DeviceDiscount;
use Discount\Repositories\Filters\DiscountFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Models\Tables\User;

class DeviceDiscountRepository
{
    public function deleteByDiscount(int $discountId): bool
    {
        return DeviceDiscount::query()->where('discountId', $discountId)->delete();
    }

    public function create(array $attributes): DeviceDiscount
    {
        $instance = new DeviceDiscount();
        $instance->fill($attributes)->save();

        return $instance;
    }

    public function devicesAvailable(User $user): Collection
    {
        $context = (new DiscountFilter())
            ->available($user->getNetwork()->id)
            ->getQuery();

        return DeviceDiscount::query()->whereHas('discountEntity', function (Builder $query) use ($context) {
            $query->mergeConstraintsFrom($context);
        })->with('device')->get();
    }
}
