<?php

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class UpdateIsPreSaleToDevicesNetwork extends Seeder
{
    private $NETWORK;

    public function __construct()
    {
        $this->NETWORK = NetworkEnum::IPLACE;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $devicesNetworks = $this->getDevicesNetworks();

        $this->updateIsPreSaleToTrue($devicesNetworks);
    }

    public function getDevicesNetworks(): Collection
    {
        return DB::table('devices_network')
            ->select('devices_network.id')
            ->join('networks', 'networks.id', 'devices_network.networkId')
            ->join('devices', 'devices.id', 'devices_network.deviceId')
            ->where('networks.slug', $this->NETWORK)
            ->where('devices.model', 'like', '%iphone 12%')
            ->get();
    }

    public function updateIsPreSaleToTrue($devicesNetworks): void
    {
        $devicesNetworks->each(function ($deviceNetwork) {
            DB::table('devices_network')
                ->where('id', $deviceNetwork->id)
                ->update([
                    'isPreSale' => true
                ]);
        });
    }
}
