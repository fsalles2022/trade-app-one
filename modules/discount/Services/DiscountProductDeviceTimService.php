<?php

declare(strict_types=1);

namespace Discount\Services;

use Discount\Repositories\DiscountProductDeviceTimRepository;

class DiscountProductDeviceTimService
{
    /** @var DiscountProductDeviceTimRepository */
    protected $discountProductDeviceTimRepository;

    public function __construct(DiscountProductDeviceTimRepository $discountProductDeviceTimRepository)
    {
        $this->discountProductDeviceTimRepository = $discountProductDeviceTimRepository;
    }

    /** @param array[] $discountsProductsDevices */
    public function createInBulk(array $discountsProductsDevices): void
    {
        $discountsProductsDevicesChunked = array_chunk($discountsProductsDevices, 1000);

        foreach ($discountsProductsDevicesChunked as $discountsProductsDevices) {
            $this->discountProductDeviceTimRepository->createInBulk($discountsProductsDevices);
        }
    }

    public function deleteAll(): void
    {
        $this->discountProductDeviceTimRepository->deleteAll();
    }
}
