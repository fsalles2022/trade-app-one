<?php

namespace Outsourced\Crafts\Devices;

interface DevicesActionsInterface
{
    public function findDevice(string $identify): OutsourcedDeviceDTO;
}
