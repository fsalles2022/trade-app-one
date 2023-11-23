<?php

declare(strict_types=1);

namespace Discount\Services;

use Discount\Models\DeviceTim;
use Discount\Repositories\DeviceTimRepository;
use Illuminate\Database\Eloquent\Collection;

class DeviceTimService
{
    /** @var DeviceTimRepository */
    protected $deviceTimRepository;

    public function __construct(DeviceTimRepository $deviceTimRepository)
    {
        $this->deviceTimRepository = $deviceTimRepository;
    }

    public function findById(int $id): ?DeviceTim
    {
        return $this->deviceTimRepository->find($id);
    }

    public function getAll(): Collection
    {
        return $this->deviceTimRepository->all();
    }

    /** @param mixed[] $attributes */
    public function create(array $attributes): DeviceTim
    {
        return $this->deviceTimRepository->create($attributes);
    }

    public function updatePrice(DeviceTim $deviceTim, float $price): DeviceTim
    {
        return $this->deviceTimRepository->update(
            $deviceTim,
            [
                'price' => $price
            ]
        );
    }
}
