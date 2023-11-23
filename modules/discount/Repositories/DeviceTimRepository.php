<?php

declare(strict_types=1);

namespace Discount\Repositories;

use Discount\Models\DeviceTim;
use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class DeviceTimRepository extends BaseRepository
{
    protected $model = DeviceTim::class;

    public function getDevicesWithDiscounts(): Collection
    {
        return $this->createModel()->newQuery()->with('products')->get();
    }
}
