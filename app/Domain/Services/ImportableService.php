<?php

namespace TradeAppOne\Domain\Services;

use Buyback\Services\DeviceService;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Models\Tables\User;

class ImportableService
{
    private $hierarchyService;
    private $deviceService;

    public function __construct(HierarchyService $hierarchyService, DeviceService $deviceService)
    {
        $this->hierarchyService = $hierarchyService;
        $this->deviceService    = $deviceService;
    }

    public function getNetworkDevices(User $user)
    {
        $networksCollection = $this->hierarchyService->getNetworksThatBelongsToUser($user);
        $networksId         = $networksCollection->pluck('id')->toArray();

        $devices = $this->deviceService->devicesByNetworksIdWithFilters($networksId);
        $devices = $devices->map(function ($item) {
            return [
                'id'    => data_get($item, 'id', ''),
                'label' => data_get($item, 'label', ''),
                'slug'  => strtoupper(str_slug(data_get($item, 'label', ''), ''))
            ];
        })->toArray();

        $csvFile = [['id', 'label', 'slug']];

        foreach ($devices as $index => $device) {
            array_push($csvFile, $device);
        }

        return CsvHelper::arrayToCsv($csvFile);
    }
}
