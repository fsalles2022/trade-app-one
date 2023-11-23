<?php

namespace Outsourced\Riachuelo\Services;

use Outsourced\Crafts\Devices\DevicesActionsInterface;
use Outsourced\Crafts\Devices\OutsourcedDeviceDTO;
use Outsourced\Riachuelo\Connections\RiachueloConnection;

class DevicesRiachueloService implements DevicesActionsInterface
{
    protected $riachueloConnection;

    public function __construct(RiachueloConnection $riachueloConnection)
    {
        $this->riachueloConnection = $riachueloConnection;
    }

    public function findDevice(string $imei): OutsourcedDeviceDTO
    {
        $device = $this->riachueloConnection->findDevice($imei);

        $sku   = trim(data_get($device, 'ean'));
        $model = trim(data_get($device, 'nome'));
        $label = data_get($device, 'descricao');

        return new OutsourcedDeviceDTO($sku, $model, $label);
    }
}
