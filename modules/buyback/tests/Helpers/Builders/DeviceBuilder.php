<?php

namespace Buyback\Tests\Helpers\Builders;

use Buyback\Models\DevicesNetwork;
use TradeAppOne\Domain\Models\Tables\Device;
use TradeAppOne\Domain\Models\Tables\Network;

class DeviceBuilder
{
    private $network;
    private $sku;
    private $type;

    public function withNetwork(Network $network): DeviceBuilder
    {
        $this->network = $network;
        return $this;
    }

    public function withSku(string $sku): DeviceBuilder
    {
        $this->sku = $sku;
        return $this;
    }

    public function generateDeviceTimes(int $quantity)
    {
        $builded = collect();
        foreach (range(1, $quantity) as $index) {
            $builded->push($this->build());
        }
        return $builded;
    }

    public function withType(string $type): DeviceBuilder
    {
        $this->type = $type;
        return $this;
    }

    public function build(): Device
    {
        $networkEntity = $this->network ?? factory(Network::class)->create();
        $deviceEntity  = factory(Device::class)->make();

        if (isset($this->type)) {
            $deviceEntity->type = $this->type;
        }

        $deviceEntity->save();

        $devicesNetwork = new DevicesNetwork();

        $devicesNetwork->network()->associate($networkEntity);
        $devicesNetwork->device()->associate($deviceEntity);
        $devicesNetwork->sku = $this->sku;
        $devicesNetwork->save();

        return $deviceEntity;
    }
}
