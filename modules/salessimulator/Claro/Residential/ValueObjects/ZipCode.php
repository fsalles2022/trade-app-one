<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\ValueObjects;

class ZipCode
{
    /** @var string|null */
    private $zipCode;

    public function __construct(?string $zipCode)
    {
        $this->zipCode = $zipCode;
    }

    public function getZipCode(): ?string
    {
        return ($this->zipCode !== null) ? preg_replace('/[^0-9]/', '', $this->zipCode) : null;
    }
}
