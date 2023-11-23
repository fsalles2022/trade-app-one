<?php

namespace VivoBR\Helpers;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\User;
use VivoBR\Helpers\Rules\FastShopRules;
use VivoBR\Helpers\Rules\VivoRegionalLesteRules;

class VivoBrPlansFilter
{
    private const NETWORK_FILTERS = [
        VivoRegionalLesteRules::class,
        FastShopRules::class
    ];

    public static function filter(Collection $plans, array $options, User $user = null): Collection
    {
        $filteredPlans = clone $plans;
        $cnpj          = data_get($user->pointsOfSale->first(), 'cnpj', '');
        $network       = data_get($user->getNetwork(), 'slug', '');
        $operation     = data_get($options, 'operation', null);

        if ($operation) {
            $filteredPlans = $plans->where('operation', $operation);
        }

        foreach (self::NETWORK_FILTERS as $filter) {
            $planFilter    = resolve($filter);
            $filteredPlans = $planFilter->hasToFilter($cnpj, $network) ? $planFilter->filter($filteredPlans) : $filteredPlans;
        }

        return $filteredPlans->values();
    }
}
