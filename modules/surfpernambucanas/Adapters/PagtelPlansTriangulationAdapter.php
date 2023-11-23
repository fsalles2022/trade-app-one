<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Plan;

class PagtelPlansTriangulationAdapter
{
    /** @param array[] $plans */
    public static function adapt(array $plans): Collection
    {
        $collectionOfPlans = collect([]);

        foreach ($plans as $plan) {
            $planAdapted            = new Plan(
                data_get($plan, 'id'),
                data_get($plan, 'label') . ' ' .  self::getInternetAdvantage($plan),
                (float) data_get($plan, 'price', 0.00),
                $plan
            );
            $planAdapted->operation = str_contains((string) mb_strtolower(data_get($plan, 'label')), 'controle inteligente') ? Operations::SURF_PERNAMBUCANAS_SMART_CONTROL : Operations::SURF_PERNAMBUCANAS_PRE;
            $planAdapted->operator  = Operations::SURF_PERNAMBUCANAS;

            if ($planAdapted instanceof Plan) {
                $collectionOfPlans->push($planAdapted);
            }
        }
        return $collectionOfPlans;
    }

    /** @param mixed[] $plan */
    private static function getInternetAdvantage(array $plan): string
    {
        $internetAdvantage = '';

        $advantages = data_get($plan, 'advantages', []);
        foreach ($advantages as $advantage) {
            if (data_get($advantage, 'alias') === 'internet') {
                $internetAdvantage = data_get($advantage, 'label', '');
                break;
            }
        }

        return $internetAdvantage;
    }
}
