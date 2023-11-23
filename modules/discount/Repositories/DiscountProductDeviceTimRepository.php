<?php

declare(strict_types=1);

namespace Discount\Repositories;

use Discount\Models\DiscountProductDeviceTim;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class DiscountProductDeviceTimRepository extends BaseRepository
{
    protected $model = DiscountProductDeviceTim::class;

    public function createInBulk(array $items): bool
    {
        return $this->createModel()->newQuery()->insert($items);
    }

    public function deleteAll(): int
    {
        return $this->createModel()->newQuery()->delete();
    }
}
