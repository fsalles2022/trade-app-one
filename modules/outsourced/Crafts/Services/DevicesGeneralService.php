<?php

namespace Outsourced\Crafts\Services;

use Outsourced\Crafts\Devices\DevicesActionsInterface;
use Outsourced\Crafts\Devices\OutsourcedDeviceDTO;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;

class DevicesGeneralService implements DevicesActionsInterface
{

    public function findDevice(string $sku): OutsourcedDeviceDTO
    {
        $device = DeviceOutSourced::where('sku', $sku)->first();

        $databaseSku = data_get($device, 'sku');
        $model       = data_get($device, 'model');
        $label       = data_get($device, 'label');

        return new OutsourcedDeviceDTO($databaseSku, $model, $label);
    }
}
