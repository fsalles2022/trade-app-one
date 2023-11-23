<?php


namespace FastShop\Importables;

use ClaroBR\Enumerators\ClaroOperations;
use FastShop\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Service;

class ClaroPlansImport
{
    protected $plans;

    private const CLARO_POS_PAGO = 'POS_PAGO';

    private const AVAILABLE_PLAN_TYPES = [
        ClaroOperations::CONTROLE_BOLETO,
        self::CLARO_POS_PAGO
    ];

    public function prepare(Collection $plans): self
    {
        $filtered = $plans->filter(static function ($plan) {
            $planType = data_get($plan, 'plan_type.nome', '');
            return in_array($planType, self::AVAILABLE_PLAN_TYPES, true);
        });

        if ($filtered->isNotEmpty()) {
            $this->plans = $filtered;
        }
        return $this;
    }

    public function import(): int
    {
        $count = 0;
        if ($this->plans->isEmpty()) {
            return $count;
        }

        $this->plans->each(static function ($plan) use (&$count) {
            $plansAreaCode = collect(data_get($plan, 'plans_area_code', []));

            $plansAreaCode->each(static function ($area) use ($plan, &$count) {
                $promotions = collect(data_get($area, 'promotions', []));

                $promotions->each(static function ($promotion) use ($plan, $area, &$count) {
                    $active = data_get($plan, 'ativo');
                    if ($active === 1) {
                        if (self::persist($plan, $area, $promotion)) {
                            $count++;
                        }
                    }
                });
            });
        });
        return $count;
    }

    private static function persist(array $plan, array $areaPlan, array $promotion): bool
    {
        $operatorCode  = data_get($promotion, 'codigo_operadora');
        $name          = data_get($promotion, 'nome');
        $areaCode      = data_get($areaPlan, 'ddd');
        $loyaltyMonths = data_get($promotion, 'fidelidade', 0);
        $price         = data_get($areaPlan, 'valor');

        $operation  = data_get($plan, 'plan_type.nome', '');
        $service    = self::findServiceId(Operations::CLARO, $operation);
        $internetGb = data_get($plan, 'gb', 0);
        $extras     = data_get($plan, 'descricao', '');

        $product = Product::updateOrCreate(
            ['code' => $operatorCode, 'areaCode' => $areaCode],
            [
                'code'          => $operatorCode,
                'title'         => $name,
                'areaCode'      => $areaCode,
                'loyaltyMonths' => $loyaltyMonths,
                'price'         => $price,
                'serviceId'     => $service->id,
                'internet'      => $internetGb,
                'minutes'       => 0,
                'extras'        => json_encode(['descricao' => $extras]),
                'original'      => json_encode($promotion)
            ]
        );
        return $product ? true : false;
    }

    private static function findServiceId(string $operator, $operation): Service
    {
        if ($operation === self::CLARO_POS_PAGO) {
            $operation = ClaroOperations::POS_PAGO;
        }

        return Service::where([
            'operator' => $operator,
            'operation' => $operation
        ])->first();
    }
}
