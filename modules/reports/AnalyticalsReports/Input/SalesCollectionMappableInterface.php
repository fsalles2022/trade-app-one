<?php

declare(strict_types=1);

namespace Reports\AnalyticalsReports\Input;

use TradeAppOne\Domain\Models\Collections\Sale;

/**
 * Interface used to construct collection of input to extract reports
 * Collection make map of sales to array.
 */
interface SalesCollectionMappableInterface
{
    /** @param Sale[] $sale */
    public function __construct(array $sales);

    /** @return array[] */
    public function toArray(): array;
}
