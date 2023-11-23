<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Models;

use Reports\SubModules\Riachuelo\Hourly\Services\SaleAccumulator;

abstract class AccumulatorCollection
{
    /** @var SaleAccumulator[] */
    protected $salesAccumulators;

    public function orderByTotalVolumeAccumulator(): self
    {
        usort(
            $this->salesAccumulators,
            function (SaleAccumulator $saleAccumulatorA, SaleAccumulator $saleAccumulatorB): bool {
                return $saleAccumulatorA->getTotalVolumeAccumulator() < $saleAccumulatorB->getTotalVolumeAccumulator();
            }
        );

        return $this;
    }

    /** @return SaleAccumulator[] */
    public function getSalesAccumulators(): array
    {
        return $this->salesAccumulators;
    }

    public function getTotalByOperator(string $operator): int
    {
        $total = 0;

        foreach ($this->salesAccumulators as $saleAccumulator) {
            $total += $saleAccumulator->getTotalVolumeByOperator($operator);
        }

        return $total;
    }

    public function getTotalVolumeAccumulator(): int
    {
        $total = 0;

        foreach ($this->salesAccumulators as $saleAccumulator) {
            $total += $saleAccumulator->getTotalVolumeAccumulator();
        }

        return $total;
    }

    public function getTotalTelecommunicationOperatorsVolumeAccumulator(): int
    {
        $total = 0;

        foreach ($this->salesAccumulators as $saleAccumulator) {
            $total += $saleAccumulator->getTotalTelecommunicationOperatorsVolumeAccumulator();
        }

        return $total;
    }
}
