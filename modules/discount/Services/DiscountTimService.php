<?php

declare(strict_types=1);

namespace Discount\Services;

use Discount\Models\DeviceTim;
use Discount\Models\DiscountProductDeviceTim;
use Discount\Repositories\DeviceTimRepository;
use Discount\Repositories\DiscountProductDeviceTimRepository;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Permissions\TimRebatePermission;
use TradeAppOne\Domain\Models\Tables\User;

class DiscountTimService
{
    /** @var DiscountProductDeviceTimRepository */
    private $discountProductDeviceTimRepository;

    /** @var DeviceTimRepository */
    private $deviceTimRepository;

    public function __construct(
        DiscountProductDeviceTimRepository $discountProductDeviceTimRepository,
        DeviceTimRepository $deviceTimRepository
    ) {
        $this->discountProductDeviceTimRepository = $discountProductDeviceTimRepository;
        $this->deviceTimRepository                = $deviceTimRepository;
    }

    public function shouldUseDiscountByOperation(User $user, string $operation): bool
    {
        if (! $user->hasPermission(TimRebatePermission::getFullName(TimRebatePermission::USE))) {
            return false;
        }

        return in_array($operation, [
            Operations::TIM_CONTROLE_FATURA,
            Operations::TIM_BLACK,
            Operations::TIM_BLACK_MULTI,
            Operations::TIM_BLACK_EXPRESS,
        ]);
    }

    public function getDiscountById(int $id): ?DiscountProductDeviceTim
    {
        return $this->discountProductDeviceTimRepository->find($id);
    }

    public function getDiscounts(): Collection
    {
        $devices   = $this->deviceTimRepository->getDevicesWithDiscounts();
        $discounts = collect([]);

        foreach ($devices as $device) {
            $products = $this->getProductsByDevice($device);

            $discounts->push([
                'id' => $device->id,
                'label' => $device->label,
                'model' => $device->model,
                'sku'   => null,
                'discount' => [],
                'products' => $products
            ]);
        }

        return $discounts;
    }

    private function getProductsByDevice(DeviceTim $device): Collection
    {
        $products = collect([]);

        foreach ($device->products as $product) {
            $products->push([
                'id'            => $product->id,
                'title'         => $product->label,
                'label'         => $product->label,
                'product'       => $product->externalIdentifier,
                'discount'      => $product->pivot->discount,
                'discountId'    => $product->pivot->id,
            ]);
        }

        return $products;
    }
}
