<?php

namespace Outsourced\Cea\Services;

use Outsourced\Cea\ConsultaSerialConnection\CeaSerialConnection;
use Outsourced\Crafts\Devices\DevicesActionsInterface;
use Outsourced\Crafts\Devices\OutsourcedDeviceDTO;

class DevicesCeaService implements DevicesActionsInterface
{
    protected $ceaConnection;

    public function __construct(CeaSerialConnection $ceaConnection)
    {
        $this->ceaConnection = $ceaConnection;
    }

    public function findDevice(string $imei): OutsourcedDeviceDTO
    {
        $device = $this->ceaConnection->findDevice($imei);

        $sku   = trim(data_get($device, 'SKU'));
        $model = trim(data_get($device, 'MODELO'));
        $label = data_get($device, 'DESCR');

        return new OutsourcedDeviceDTO($sku, $model, $label);
    }
}
