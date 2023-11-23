<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Services;

use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class PointOfSaleSaleAccumulator extends BaseSaleAccumulator implements SaleAccumulator
{
    /** @var PointOfSale */
    protected $pointOfSale;

    public function __construct(PointOfSale $pointOfSale)
    {
        $this->pointOfSale = $pointOfSale;
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

    public function getPointOfSale(): PointOfSale
    {
        return $this->pointOfSale;
    }
}
