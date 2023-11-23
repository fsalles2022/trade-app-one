<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Services;

use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\Hierarchy;

class HierarchySaleAccumulator extends BaseSaleAccumulator implements SaleAccumulator
{
    /** @var Hierarchy */
    protected $hierarchy;

    public function __construct(Hierarchy $hierarchy)
    {
        $this->hierarchy = $hierarchy;
    }

    public function accumulate(Sale $sale): void
    {
        /** @var Service[] $services */
        $services = $sale->services;

        foreach ($services as $service) {
            $this->accumulateOperator($service);
            $this->accumulateTotal();
        }

        $this->sales[] = $sale;
    }

    public function getHierarchy(): Hierarchy
    {
        return $this->hierarchy;
    }
}
