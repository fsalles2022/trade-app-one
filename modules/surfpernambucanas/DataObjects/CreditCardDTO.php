<?php

declare(strict_types=1);

namespace SurfPernambucanas\DataObjects;

class CreditCardDTO
{
    /** @var string */
    protected $number;

    /** @var string */
    protected $cvv;

    /** @var string */
    protected $yearValidity;

    /** @var string */
    protected $monthValidity;

    public function __construct(
        string $number,
        string $cvv,
        string $yearValidity,
        string $monthValidity
    ) {
        $this->number        = $number;
        $this->cvv           = $cvv;
        $this->yearValidity  = $yearValidity;
        $this->monthValidity = $monthValidity;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getCvv(): string
    {
        return $this->cvv;
    }

    public function getValidity(): string
    {
        return "{$this->monthValidity}/{$this->yearValidity}";
    }
}
