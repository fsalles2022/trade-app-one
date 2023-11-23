<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Entities;

class Address
{
    /** @var string|null */
    private $cityId;

    /** @var string|null */
    private $operatorCode;

    /** @var string|null */
    private $stateAcronym;

    /** @var bool|null */
    private $withViability;

    public function __construct(
        ?string $cityId,
        ?string $operatorCode,
        ?string $stateAcronym,
        bool $withViability
    ) {
        $this->cityId        = $cityId ?? null;
        $this->operatorCode  = $operatorCode ?? null;
        $this->stateAcronym  = $stateAcronym ?? null;
        $this->withViability = $withViability;
    }

    public function getCityId(): ?string
    {
        return $this->cityId;
    }

    public function getOperatorCode(): ?string
    {
        return $this->operatorCode;
    }

    public function getStateAcronym(): ?string
    {
        return $this->stateAcronym;
    }

    public function withViability(): ?bool
    {
        return $this->withViability;
    }

    public function isEmptyOperatorCodeOrStateAcronym(): bool
    {
        return empty($this->operatorCode) || empty($this->stateAcronym);
    }
}
