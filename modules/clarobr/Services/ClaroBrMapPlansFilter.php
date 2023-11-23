<?php

namespace ClaroBR\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Modes;

class ClaroBrMapPlansFilter
{
    public static function filter(Collection $plans, array $filters): Collection
    {
        $filteredPlans = clone $plans;
        $operation     = data_get($filters, 'operation', null);
        $product       = data_get($filters, 'product', null);
        $mode          = data_get($filters, 'mode', null);

        if ($operation) {
            $operationsFilter = [];
            if (is_string($operation)) {
                $operationsFilter[] = $operation;
            }
            if (is_array($operation)) {
                $operationsFilter = $operation;
            }
            $filteredPlans = $filteredPlans->filter(static function ($item) use ($operationsFilter) {
                return in_array($item->operation, $operationsFilter, true) ? $item->operation : null;
            })->values();
        }

        if ($product) {
            $filteredPlans = $filteredPlans->filter(static function ($item) use ($product) {
                return $item->product === (string) $product;
            })->values();
        }

        if ($mode) {
            $filteredPlans = $filteredPlans->filter(static function ($item) use ($mode) {

                // In SIV Portability not fully changed, Still usings Activation Plans.
                if ($mode === Modes::PORTABILITY) {
                    return $item->mode === Modes::ACTIVATION;
                }

                return $item->mode === $mode;
            })->values();
        }

        return $filteredPlans;
    }
}
