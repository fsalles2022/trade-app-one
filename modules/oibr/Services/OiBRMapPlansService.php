<?php

namespace OiBR\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;

class OiBRMapPlansService
{
    const BLACKLIST_PLANS = ['OCSF158'];

    public static function map(array $plans, $options = []): Collection
    {
        $collectionOfPlans = new Collection();
        foreach ($plans as $plan) {
            $newPlan['product']       = data_get($plan, 'nome');
            $newPlan['label']         = data_get($plan, 'nomeComercial');
            $newPlan['adhesionValue'] = data_get($plan, 'valorAdesao') / 100;
            $newPlan['price']         = data_get($plan, 'valorRecorrencia') / 100;
            $newPlan['details']       = [data_get($plan, 'descricao')];
            $newPlan['operation']     = data_get($options, 'operation');
            $newPlan['operator']      = Operations::OI;
            $newPlan['oi']            = $plan;
            if ($newPlan['product'] == 'OCSF114') {
                $newPlan['label'] = 'B - Oi Mais Controle Básico G3 - R$39,99';
            }

            $collectionOfPlans->push($newPlan);
        }
        return $collectionOfPlans->whereNotIn('product', self::BLACKLIST_PLANS);
    }
}
