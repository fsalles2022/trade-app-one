<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Services;

use TradeAppOne\Domain\Models\Collections\Sale;

/** Contract to implement sale accumulation */
interface SaleAccumulator
{
    public function accumulate(Sale $sale): void;
    public function getTotalVolumeByOperator(string $operator): int;
    public function getTotalVolumeAccumulator(): int;
}
