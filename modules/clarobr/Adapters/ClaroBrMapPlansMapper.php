<?php

namespace ClaroBR\Adapters;

use ClaroBR\Enumerators\ClaroBrModes;
use ClaroBR\Enumerators\ClaroInvoiceTypes;
use ClaroBR\Enumerators\ClaroOperations;
use ClaroBR\Models\PlanClaro;
use ClaroBR\Models\PromotionsClaro;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\Operations;

class ClaroBrMapPlansMapper
{
    public static function map(array $plans): Collection
    {
        $collectionOfPlans = new Collection();
        foreach ($plans as $plan) {
            $plansByAreaCode = data_get($plan, 'plans_area_code', []);

            foreach ($plansByAreaCode as $planAreaCode) {
                $promotions = ClaroBrPromotionMapper::map($planAreaCode);

                if ($promotions->isEmpty()) {
                    $adapted = self::adaptPlan($plan, $planAreaCode);

                    if ($adapted instanceof PlanClaro) {
                        $collectionOfPlans->push($adapted);
                    }
                }

                foreach ($promotions as $promotion) {
                    $adapted = self::adaptPlan($plan, $planAreaCode, $promotion);

                    if ($adapted instanceof PlanClaro) {
                        $collectionOfPlans->push($adapted);
                    }
                }
            }
        }

        return $collectionOfPlans;
    }

    private static function adaptPlan($plan, $planAreaCode, ?PromotionsClaro $promotion = null)
    {
        $product      = data_get($plan, 'id');
        $label        = data_get($plan, 'label');
        $price        = data_get($planAreaCode, 'valor');
        $areaCode     = data_get($planAreaCode, 'ddd');
        $operatorCode = data_get($plan, 'codigo_operadora');

        $planType  = data_get($plan, 'plan_type.nome', null);
        $operation = ConstantHelper::getValue(ClaroOperations::class, $planType);

        $extractedFaturas       = array_wrap(data_get($plan, 'faturas', []));
        $faturasKeys            = array_keys($extractedFaturas);
        $invoiceTypesAvailables = ConstantHelper::getGroupOfValues(ClaroInvoiceTypes::class, $faturasKeys);
        $mode                   = $promotion ? $promotion->mode : null;

        if (is_null($mode) && ($operation === Operations::CLARO_CONTROLE_FACIL)) {
            $mode = ClaroBrModes::MIGRACAO;
        }

        if (is_null($product)
            || is_null($label)
            || is_null($price)
            || is_null($areaCode)
            || is_null($operation)
            || is_null($invoiceTypesAvailables)
            || is_null($mode)) {
            return null;
        }

        $plan = new PlanClaro($product, $label, $price, $plan);

        $plan->operation    = $operation;
        $plan->operator     = Operations::CLARO;
        $plan->invoiceTypes = $invoiceTypesAvailables;
        $plan->areaCode     = $areaCode;
        $plan->promotion    = $promotion;
        $plan->mode         = $mode;
        $plan->operatorCode = $operatorCode;

        return $plan;
    }
}
