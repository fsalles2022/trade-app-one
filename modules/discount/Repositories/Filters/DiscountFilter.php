<?php

namespace Discount\Repositories\Filters;

use Carbon\Carbon;
use Discount\Enumerators\DiscountStatus;
use Discount\Models\Discount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Policies\Authorizations;
use TradeAppOne\Domain\Repositories\Filters\BaseFilters;

class DiscountFilter extends BaseFilters
{
    protected $query;

    public function __construct(Builder $query = null)
    {
        $this->query = $query ?? Discount::query();
    }

    public function title($value): DiscountFilter
    {
        $this->query->where('title', 'like', "%$value%");
        return $this;
    }

    public function operation($value): DiscountFilter
    {
        $this->query->whereHas('products', static function ($queryBuilder) use ($value) {
            $queryBuilder->whereIn('operation', array_wrap($value));
        });
        return $this;
    }

    public function operator($values): DiscountFilter
    {
        $this->query->whereHas('products', static function ($queryBuilder) use ($values) {
            $queryBuilder->whereIn('operator', array_wrap($values));
        });
        return $this;
    }

    public function product($value): DiscountFilter
    {
        $this->query->whereHas('products', static function ($queryBuilder) use ($value) {
            $queryBuilder->where('product', '=', $value);
        });
        return $this;
    }

    public function promotion($value): DiscountFilter
    {
        $this->query->whereHas('products', static function ($queryBuilder) use ($value) {
            $queryBuilder->where('promotion', '=', $value);
        });
        return $this;
    }

    public function status($value): DiscountFilter
    {
        $this->query->where('status', '=', $value);
        return $this;
    }

    public function devices($values): DiscountFilter
    {
        $this->query->whereHas('devices', static function ($query) use ($values) {
            $query->whereIn('deviceId', array_wrap($values));
        });

        return $this;
    }

    public function model($value): DiscountFilter
    {
        $this->query->whereHas('devices', static function ($discountsQuery) use ($value) {
            $discountsQuery->whereHas('device', static function ($devicesDiscountsQuery) use ($value) {
                $devicesDiscountsQuery->with('device')
                    ->where('model', 'like', "%$value%")
                    ->orWhere('label', 'like', "%$value%");
            });
        });

        return $this;
    }

    public function startAt($value): DiscountFilter
    {
        $this->query->where('startAt', '>=', $value);
        return $this;
    }

    public function endAt($value): DiscountFilter
    {
        $this->query->where('endAt', '<=', $value);
        return $this;
    }

    public function updatedAt($value): DiscountFilter
    {
        $this->query->where('updatedAt', '>=', $value);
        return $this;
    }

    public function networkSlug($value): DiscountFilter
    {
        $this->query->whereHas('network', static function ($query) use ($value) {
            $query->where('slug', '=', $value);
        });
        return $this;
    }

    public function networks($value): DiscountFilter
    {
        $this->query->whereHas('network', static function ($query) use ($value) {
            $query->whereIn('slug', array_wrap($value));
        });
        return $this;
    }

    public function pointsOfSale($values): DiscountFilter
    {
        $this->query->whereHas('pointsOfSale', static function (Builder $query) use ($values) {
                $query->whereIn('cnpj', array_wrap($values));
        });

        return $this;
    }

    public function sku($value): DiscountFilter
    {
        $this->query->whereHas('devices.device', static function (Builder $query) use ($value) {
            $query->where('sku', '=', $value);
        });

        return $this;
    }

    public function intervalStrategy($startAt): DiscountFilter
    {
        $this->query->where(static function (Builder $query) use ($startAt) {
                $query->where('startAt', '>', $startAt)
                    ->orWhere('endAt', '>', $startAt);
        });

        return $this;
    }

    public function available($networkId): DiscountFilter
    {
        $this->query
            ->where('startAt', '<=', Carbon::now())
            ->where('endAt', '>=', Carbon::now())
            ->where('networkId', $networkId)
            ->where('status', DiscountStatus::ACTIVE);

        return $this;
    }

    public function byContext(User $user): DiscountFilter
    {
        $authorizations = $this->authorizations()->setUser($user);
        $roles          = $authorizations->getRolesAuthorized()->push($user->role);

        $users = $authorizations
            ->setRoles($roles)
            ->getUsersAuthorized();

        $this->query->whereHas('user', static function (Builder $query) use ($users) {
            $query->mergeConstraintsFrom($users);
        });

        return $this;
    }

    private function authorizations(): Authorizations
    {
        return resolve(Authorizations::class);
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function get(): Collection
    {
        return $this->query->get();
    }
}
