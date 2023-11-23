<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Models;

use Reports\SubModules\Riachuelo\Hourly\Services\HierarchySaleAccumulator;

class HierarchySaleAccumulatorCollection extends AccumulatorCollection
{
    /** @var HierarchySaleAccumulator[] */
    protected $salesAccumulators;

    /** @param HierarchySaleAccumulator[] $salesAccumulators */
    public function __construct(array $salesAccumulators)
    {
        $this->salesAccumulators = $salesAccumulators;
    }
}
