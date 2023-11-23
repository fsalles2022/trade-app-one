<?php

namespace TradeAppOne\Domain\Adapters;

use Illuminate\Support\Collection;

interface MapPlans
{
    public static function map(array $plans, $filters = []): Collection;
}
