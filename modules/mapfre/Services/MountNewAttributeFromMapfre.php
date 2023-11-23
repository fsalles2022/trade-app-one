<?php

namespace Mapfre\Services;

use TradeAppOne\Domain\Services\MountNewAttributesService;

class MountNewAttributeFromMapfre implements MountNewAttributesService
{
    protected $connection;

    public function getAttributes(array $service): array
    {
        return [];
    }
}
