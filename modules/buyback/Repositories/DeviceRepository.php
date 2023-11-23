<?php

namespace Buyback\Repositories;

use TradeAppOne\Domain\Models\Tables\Device;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class DeviceRepository extends BaseRepository
{
    protected $model = Device::class;

    public function findAllByNetworksIdWithFilters(array $networksId, array $filters = []): ?Collection
    {
        $deviceList = $this->createModel()->whereHas('networks', function ($query) use ($networksId, &$filters) {
            $query->whereIn('devices_network.networkId', $networksId);

            if (data_get($filters, 'devices_network.isPreSale', null) !== null) {
                $query->where('devices_network.isPreSale', 0);
                unset($filters['devices_network']);
            }
        });

        foreach ($filters as $key => $value) {
            $deviceList->where($key, $value);
        }

        return $deviceList->get();
    }

    public function findOneDeviceByDeviceIdAndNetworkId(int $deviceId, int $networkId): ?array
    {
        $pivotDevicesNetwork = DB::table('devices_network')
            ->where(['networkId' => $networkId, 'deviceId' => $deviceId])
            ->first();

        if (is_null($pivotDevicesNetwork)) {
            return null;
        }

        return [
            'id'        => $pivotDevicesNetwork->id,
            'deviceId'  => $pivotDevicesNetwork->deviceId,
            'networkId' => $pivotDevicesNetwork->networkId
        ];
    }

    public function findDeviceTierNotes()
    {
        return DB::table('deviceTier')->first();
    }
}
