<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Entities;

use SalesSimulator\Claro\Residential\ValueObjects\ZipCode;

class Customer
{
    /** @var ZipCode */
    private $zipCode;

    public function __construct(ZipCode $zipCode)
    {
        $this->zipCode = $zipCode;
    }

    public function getZipCode(): ZipCode
    {
        return $this->zipCode;
    }
}
