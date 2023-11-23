<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Factories;

use SalesSimulator\Claro\Residential\Entities\Address;

class AddressFactory
{
    public static function create(
        ?string $cityId,
        ?string $operatorCode,
        ?string $state,
        bool $withViability
    ): Address {
        return new Address(
            $cityId,
            $operatorCode,
            $state,
            $withViability
        );
    }
}
