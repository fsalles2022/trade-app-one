<?php

declare(strict_types=1);

namespace Reports\AnalyticalsReports\Input;

use TradeAppOne\Domain\Models\Collections\Sale;

/**
 * Interface transform sale to Array on custom format.
 */
interface SaleMappableInterface
{
    public function __construct(Sale $sale);

    /** @return mixed[] */
    public function toArray(): array;
}
