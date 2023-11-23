<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Factories;

use SalesSimulator\Claro\Residential\Entities\Customer;
use SalesSimulator\Claro\Residential\ValueObjects\ZipCode;

class CustomerFactory
{
    /** @param mixed[] $attributes */
    public static function createCustomer(array $attributes): Customer
    {
        return new Customer(
            new ZipCode($attributes['zipCode'] ?? null)
        );
    }
}
