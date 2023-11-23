<?php

namespace VivoBR\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Adapters\MapPlans;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Plan;
use VivoBR\Enumerators\VivoInvoiceType;
use VivoBR\Enumerators\VivoOperations;

class VivoBrMapPlansService implements MapPlans
{
    const DEPENDENTS_RECOGNITION = 'Familia';
    const DEPENDENTS_AMOUNT      = 5;

    public static function map(array $plans, $filters = []): Collection
    {
        $plans = data_get($plans, 'planos', []);

        $collectionOfPlans = new Collection();
        foreach ($plans as $plan) {
            $adapted = self::adaptPlan($plan);

            if ($adapted instanceof Plan) {
                $collectionOfPlans->push($adapted);
            }
        }

        return $collectionOfPlans;
    }

    private static function adaptPlan($plan): ?Plan
    {
        $product  = data_get($plan, 'id');
        $label    = data_get($plan, 'nome');
        $price    = data_get($plan, 'valor');
        $areaCode = data_get($plan, 'ddd');

        $operation              = self::getOperation($plan);
        $invoiceTypesAvailables = self::getInvoiceTypes($plan);
        $dependents             = self::getAmountDependents($operation, $label);
        if (is_null($operation) || is_null($product) || is_null($label) || is_null($price) || is_null($plan) ||
            is_null($areaCode)) {
            return null;
        }

        $plan               = new Plan($product, $label, $price, $plan);
        $plan->operation    = $operation;
        $plan->operator     = Operations::VIVO;
        $plan->invoiceTypes = $invoiceTypesAvailables;
        $plan->areaCode     = $areaCode;
        $plan->dependents   = $dependents;

        return $plan;
    }

    private static function getOperation($plan)
    {
        $availablePlans = ConstantHelper::getAllConstants(VivoOperations::class);
        $tipo           = $plan['tipo'] ?? null;
        $operation      = $availablePlans[$tipo] ?? null;

        return $operation;
    }

    private static function getInvoiceTypes(array $plan): array
    {
        $tipoFaturas            = data_get($plan, 'tipoFaturas', []);
        $invoiceTypesAvailables = ConstantHelper::getAllConstants(VivoInvoiceType::class);
        $invoiceTypesAvailables = array_intersect($tipoFaturas, $invoiceTypesAvailables);
        //TODO: Remover comentários quando Vivo indicar que débito automático já está disponível
        switch (self::getOperation($plan)) {
            case Operations::VIVO_CONTROLE:
            case Operations::VIVO_POS_PAGO:
                $invoiceTypesAvailables[] = VivoInvoiceType::DEBITO_AUTOMATICO;
                break;
        }
        return $invoiceTypesAvailables;
    }

    private static function getAmountDependents(?string $operation, ?string $label): int
    {
        $containsRecognition = strpos($label, self::DEPENDENTS_RECOGNITION) !== false;
        $planIsPosPago       = $operation === Operations::VIVO_POS_PAGO;

        if ($planIsPosPago && $containsRecognition) {
            return self::DEPENDENTS_AMOUNT;
        }

        return 0;
    }
}
