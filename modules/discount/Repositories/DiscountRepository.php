<?php

namespace Discount\Repositories;

use Discount\Exceptions\DiscountExceptions;
use Discount\Models\Discount;
use Discount\Models\DiscountProduct;
use Discount\Repositories\Filters\DiscountFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\User;

class DiscountRepository
{
    public function filter(User $user, array $filters): Builder
    {
        return (new DiscountFilter())
            ->apply($filters)
            ->byContext($user)
            ->getQuery()
            ->orderBy('createdAt', 'desc')
            ->with('pointsOfSale:label,cnpj', 'devices', 'products', 'user', 'network:id,slug,label');
    }

    public function discountsAvailable(Network $network, array $filters)
    {
        return (new DiscountFilter())
            ->apply($filters)
            ->available($network->id)
            ->getQuery()
            ->with('devices.device', 'pointsOfSale', 'products')
            ->get();
    }

    public function findByNetworkAndId(Network $network, $discountId): Discount
    {
        $triangulation = (new DiscountFilter())
            ->available($network->id)
            ->getQuery()
            ->where('id', $discountId)
            ->with('devices')
            ->first();

        throw_if($triangulation === null, DiscountExceptions::notFound());
        return $triangulation;
    }

    public function create(array $attributes = []): Discount
    {
        $model = new Discount();
        $model->fill($attributes)->save();
        return $model;
    }

    public function update(Discount $instance, array $attributes = []): Discount
    {
        $instance->fill($attributes);

        $instance->save();
        $instance->touch();

        return $instance;
    }

    public function findById(int $id): ?Discount
    {
        $discount = Discount::query()->find($id);
        throw_if($discount === null, DiscountExceptions::notFound());

        return $discount;
    }
    /** @return array[] */
    public function findMany(array $ids)
    {
        $discount = Discount::query()->findMany($ids);
        throw_if($discount === [], DiscountExceptions::notFound());

        return $discount;
    }

    public function findByDiscountAndDevice($discount, $device)
    {
        $queryBuilder = $this->createModel();

        $queryBuilder = $queryBuilder->where('id', $discount);
        $queryBuilder->whereHas('devices', function ($query) use ($device) {
            $query->where('deviceId', $device);
        });

        return $queryBuilder->get();
    }

    public function findByDevicesAndPointsOfSale(array $devicesId, $startAt, array $operators, array $operations)
    {
        return (new DiscountFilter())
            ->devices($devicesId)
            ->intervalStrategy($startAt)
            ->operator($operators)
            ->operation($operations)
            ->getQuery()
            ->with(['devices', 'products'])
            ->get();
    }

    public function triangulationsAuthorized(User $user): Collection
    {
        return (new DiscountFilter())
            ->byContext($user)
            ->get();
    }
    public function removeAllWithPromotionIds($promotionIds)
    {
        DiscountProduct::query()->whereIn('promotion', $promotionIds)->delete();
    }
}
