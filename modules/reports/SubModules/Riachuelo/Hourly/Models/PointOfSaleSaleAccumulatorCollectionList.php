<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Models;

class PointOfSaleSaleAccumulatorCollectionList extends AccumulatorCollection
{
    /** @var PointOfSaleSaleAccumulatorCollection[] */
    protected $pointOfSaleSaleAccumulatorCollections;

    /** @param PointOfSaleSaleAccumulatorCollection[] $pointOfSaleSaleAccumulatorCollections */
    public function __construct(array $pointOfSaleSaleAccumulatorCollections)
    {
        $this->pointOfSaleSaleAccumulatorCollections = $pointOfSaleSaleAccumulatorCollections;
    }

    /** @return PointOfSaleSaleAccumulatorCollection[] */
    public function getPointOfSaleSaleAccumulatorCollections(): array
    {
        return $this->pointOfSaleSaleAccumulatorCollections;
    }
}
