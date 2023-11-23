<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use TradeAppOne\Domain\Models\Tables\Device;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;

class DeviceRepository
{
    protected $model = Device::class;

    public function filter($filters = [], $operator = 'like'): Builder
    {
        $devices = DB::table('devices');

        foreach ($filters as $key => $value) {
            $value   = $operator == 'like' ? "%$value%" : $value;
            $devices = $devices->where($key, $operator, $value);
        }

        return $devices;
    }

    public function paginate(Builder $queryBuilder, int $perPage): LengthAwarePaginator
    {
        return $queryBuilder->paginate($perPage);
    }

    public function all($attributes = ['*']): Collection
    {
        return $this->model::get($attributes);
    }

    public function allTypes(): Collection
    {
        return collect(Device::DEVICE_TYPES);
    }

    public static function filterByType($type): Collection
    {
        return Device::query()->where('type', '=', $type)->get();
    }

    public function devicesOutsourcedByNetwork(array $networksId): Collection
    {
        return DeviceOutSourced::query()
            ->whereIn('networkId', $networksId)
            ->get();
    }
}
