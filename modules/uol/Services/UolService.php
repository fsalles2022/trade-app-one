<?php

namespace Uol\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Plan;
use Uol\Enumerators\UolPlansEnum;

class UolService
{
    public function getPlans(): Collection
    {
        $plans = collect();

        foreach (UolPlansEnum::PLANS_AVAILABLE as $plan) {
            $adapter = new Plan(
                $plan,
                array_get(UolPlansEnum::LABEL, $plan),
                array_get(UolPlansEnum::PRICES, $plan),
                array_get(UolPlansEnum::DETAILS, $plan)
            );

            $adapter->operator  = Operations::UOL;
            $adapter->operation = array_get(UolPlansEnum::NAME, $plan);

            $plans->push($adapter);
        }

        return $plans;
    }
}
