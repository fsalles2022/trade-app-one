<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Services;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;

abstract class BaseSaleAccumulator
{
    /** @var Sale[] */
    protected $sales;

    /** @var int[] */
    protected $operatorsAccumulator = [];

    /** @var int */
    protected $totalVolumeAccumulator = 0;

    /** @var int */
    protected $totalTelecommunicationOperatorsVolumeAccumulator = 0;

    public const RESIDENTIAL_OPERATOR_ACCUMULATOR_INDEX = 'RESIDENTIAL';

    protected function accumulateOperator(Service $service): void
    {
        $operator = $this->getOperatorByService($service);

        if (array_key_exists($operator, Operations::TELECOMMUNICATION_OPERATORS)) {
            $this->totalTelecommunicationOperatorsVolumeAccumulator += 1;
        }

        if (array_key_exists($operator, $this->operatorsAccumulator) === false) {
            $this->operatorsAccumulator[$operator] = 0;
        }

        $this->operatorsAccumulator[$operator] += 1;
    }

    protected function accumulateTotal(): void
    {
        $this->totalVolumeAccumulator += 1;
    }

    protected function getOperatorByService(Service $service): string
    {
        // Is claro residential
        if (in_array($service->operation, Operations::CLARO_RESIDENTIAL)) {
            return self::RESIDENTIAL_OPERATOR_ACCUMULATOR_INDEX;
        }

        return $service->operator;
    }

    public function getTotalVolumeByOperator(string $operator): int
    {
        return $this->operatorsAccumulator[$operator] ?? 0;
    }

    public function getTotalVolumeAccumulator(): int
    {
        return $this->totalVolumeAccumulator;
    }

    public function getTotalTelecommunicationOperatorsVolumeAccumulator(): int
    {
        return $this->totalTelecommunicationOperatorsVolumeAccumulator;
    }
}
