<?php

namespace Discount\Tests\Helpers\Builders;

use Carbon\Carbon;
use Discount\Enumerators\DiscountModes;
use Discount\Enumerators\DiscountStatus;
use Discount\Models\DeviceDiscount;
use Discount\Models\Discount;
use Discount\Models\DiscountProduct;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;

class DiscountBuilder
{
    private $deviceDiscount;
    private $discountProduct;
    private $device;
    private $pointOfSale;
    private $user;
    private $startAt;
    private $endAt;
    private $network;
    private $filterMode;
    private $status;

    public function withDeviceDiscount(DeviceDiscount $deviceDiscount): DiscountBuilder
    {
        $this->deviceDiscount = $deviceDiscount;
        return $this;
    }

    public function withDevice(DeviceOutSourced $device): DiscountBuilder
    {
        $this->device = $device;
        return $this;
    }

    public function withPointOfSale(PointOfSale $pointOfSale): DiscountBuilder
    {
        $this->pointOfSale = $pointOfSale;
        return $this;
    }

    public function withStatus(string $status)
    {
        $this->status = $status;
        return $this;
    }

    public function generateDiscountTimes(int $quantity)
    {
        $builded = collect();
        foreach (range(1, $quantity) as $index) {
            $builded->push($this->build());
        }
        return $builded;
    }

    public function build(): Discount
    {
        $deviceDiscountEntity = $this->deviceDiscount ?? factory(DeviceDiscount::class)->make();
        $discountFiler        = $this->discountProduct ?? factory(DiscountProduct::class)->make();
        $deviceOutSourced     = $this->device ?? factory(DeviceOutSourced::class)->make();
        $pointOfSale          = $this->pointOfSale ?? static::createPointOfSale();
        $network              = filled($this->pointOfSale) ? $this->pointOfSale->network : factory(Network::class)->create();
        $user                 = $this->user ?? $this->createUser($network);
        $discountAttributes   = [
            'userId'    => $user->id,
            'networkId' => $network->id,
            'status'    => $this->status ?? DiscountStatus::ACTIVE
        ];
        if ($this->startAt) {
            $discountAttributes['startAt'] = $this->startAt;
        }

        if ($this->endAt) {
            $discountAttributes['endAt'] = $this->endAt;
        }
        if ($this->filterMode) {
            $discountAttributes['filterMode'] = $this->filterMode;
        }
        if ($this->network) {
            $this->network->save();
            $discountAttributes['networkId'] = $this->network->id;
        }
        $deviceOutSourced->network()->associate($network)->save();
        $discountEntity = factory(Discount::class)->create($discountAttributes);
        $discountEntity->pointsOfSale()->attach($pointOfSale);
        $deviceDiscountEntity->device()->associate($deviceOutSourced);
        $discountFiler->discount()->associate($discountEntity)->save();
        $deviceDiscountEntity->discountEntity()->associate($discountEntity)->save();

        return $discountEntity;
    }

    private static function createPointOfSale()
    {
        $network     = factory(Network::class)->create();
        $pointOfSale = factory(PointOfSale::class)->make();
        $pointOfSale->network()->associate($network)->save();
        return $pointOfSale;
    }

    private function createUser($nework)
    {
        $role = (new RoleBuilder())->withNetwork($nework)->build();
        return factory(User::class)->create(['roleId' => $role->id]);
    }

    public function available(Network $network)
    {
        $this->startAt = now();
        $this->endAt   = now()->addDays(4);
        $this->network = $network;
        $this->status  = DiscountStatus::ACTIVE;
        return $this;
    }

    public function filterModeAll()
    {
        $this->filterMode = DiscountModes::ALL;
        return $this;
    }

    public function filterModeChosen()
    {
        $this->filterMode = DiscountModes::CHOSEN;
        return $this;
    }

    public function startAt(Carbon $startAt)
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function endAt(Carbon $endAt)
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function withProduct(DiscountProduct $product)
    {
        $this->discountProduct = $product;
        return $this;
    }

    public function withUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function withNetwork(Network $network)
    {
        $this->network = $network;
        return $this;
    }
}
