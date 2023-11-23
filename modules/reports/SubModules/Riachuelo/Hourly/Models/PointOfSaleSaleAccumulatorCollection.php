<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Models;

use Reports\SubModules\Riachuelo\Hourly\Services\PointOfSaleSaleAccumulator;
use TradeAppOne\Domain\Models\Tables\Hierarchy;

class PointOfSaleSaleAccumulatorCollection extends AccumulatorCollection
{
    /** @var Hierarchy */
    protected $hierarchy;

    /** @var PointOfSaleSaleAccumulator[] */
    protected $salesAccumulators;

    /** @param PointOfSaleSaleAccumulator[] $salesAccumulators */
    public function __construct(
        Hierarchy $hierarchy,
        array $salesAccumulators
    ) {
        $this->hierarchy         = $hierarchy;
        $this->salesAccumulators = $salesAccumulators;
    }

    public function orderByPointsOfSaleHierarchies(): self
    {
        usort(
            $this->salesAccumulators,
            function (PointOfSaleSaleAccumulator $saleAccumulatorA, PointOfSaleSaleAccumulator $saleAccumulatorB): int {
                $saleAccumulatorAHierarchy = $saleAccumulatorA->getPointOfSale()->hierarchy->slug;
                $saleAccumulatorBHierarchy = $saleAccumulatorB->getPointOfSale()->hierarchy->slug;

                if ($saleAccumulatorAHierarchy === $saleAccumulatorBHierarchy) {
                    return strcmp(
                        mb_strtolower($saleAccumulatorA->getPointOfSale()->state),
                        mb_strtolower($saleAccumulatorB->getPointOfSale()->state)
                    );
                }

                return strcmp(
                    $saleAccumulatorAHierarchy,
                    $saleAccumulatorBHierarchy
                );
            }
        );

        return $this;
    }

    public function getHierarchy(): Hierarchy
    {
        return $this->hierarchy;
    }
}
