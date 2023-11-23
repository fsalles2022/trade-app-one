<?php

namespace Discount\Adapters;

use ClaroBR\Services\ClaroPromotionsService;
use ClaroBR\Services\SivService;
use Illuminate\Support\Collection;
use NextelBR\Services\NextelBRService;
use OiBR\Assistance\OiBRService;
use SurfPernambucanas\Adapters\PagtelPlansTriangulationAdapter;
use SurfPernambucanas\Services\PagtelService;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\Sale\ServiceOptionsFilter;
use VivoBR\Services\VivoBRService;
use VivoTradeUp\Repositories\VivoM4uControleCartao;

class ProductAdapter
{
    const TRADE_UP_GROUP = "tradeup-group";

    public static function adapt(Collection $availableOperators, User $user, array $operatorsFilter): Collection
    {
        $operators       = self::adaptOperators($availableOperators);
        $operations      = self::adaptOperations($availableOperators);
        $products        = self::adaptProducts($availableOperators, $user, $operatorsFilter);
        $promotions      = self::adaptPromotions();
        $operatorsFilter = collect($operatorsFilter)->keys()->all();

        return collect([
            'operators' => $operators,
            'operations' => $operations,
            'products' => $operatorsFilter
                ? $products->whereIn('operation', $operations->pluck('id'))->whereIn('operator', $operatorsFilter)->values()
                : $products->whereIn('operation', $operations->pluck('id'))->values(),
            'promotions' => $promotions
        ]);
    }

    private static function adaptOperators(Collection $availableOperators): Collection
    {
        return $availableOperators->keys()->map(function ($operator) {
            return [
                'id' => $operator,
                'label' => ucwords(strtolower($operator))
            ];
        });
    }

    private static function adaptOperations(Collection $availableOperators): Collection
    {
        return $availableOperators->flatMap(static function ($operations, $operator) {
            return array_map(static function ($operation) use ($operator) {
                $label = Service::query()
                    ->where('operator', '=', $operator)
                    ->where('operation', '=', $operation)
                    ->get()->pluck('label')->first();
                return [
                    'operator' => $operator,
                    'id' => $operation,
                    'label' => $label ?? trans("operations." . $operation . ".label")
                ];
            }, $operations);
        });
    }

    private static function adaptProducts(Collection $availableOperators, User $user, array $operatorFilters = []): Collection
    {
        $network = $user->getNetwork()->slug;
        $network = $network === self::TRADE_UP_GROUP ? NetworkEnum::CEA : $network;

        $products = collect();

        foreach ($availableOperators as $operator => $operations) {
            try {
                switch ($operator) {
                    case Operations::CLARO:
                        $sentinel    = config('integrations.siv.sentinel');
                        $mockUser    = (new User())->setAttribute('cpf', $sentinel);
                        $sivService  = resolve(SivService::class);
                        $sivProducts = $sivService->products(['areaCode' => '11'], $mockUser)->unique('product');
                        $products    = $products->merge($sivProducts);
                        break;
                    case Operations::VIVO:
                        $vivoProducts = self::selectCorrectVivoIntegration($user, $network);
                        $products     = $products->merge($vivoProducts);
                        break;
                    case Operations::NEXTEL:
                        $nextelBRService = resolve(NextelBRService::class);
                        $nextelProducts  = $nextelBRService->getProducts();
                        $products        = $products->merge($nextelProducts);
                        break;
                    case Operations::OI:
                        $hierarchyService = resolve(HierarchyService::class);
                        $pointOfSale      = $hierarchyService->getPointsOfSaleThatBelongsToUser($user)
                        ->where('providerIdentifiers.OI', '!=', null)
                        ->first();
                        $dddToSearch      = data_get($operatorFilters, Operations::OI, ['11']);
                        if ($pointOfSale) {
                            $oiBRService = resolve(OiBRService::class);
                            foreach ($dddToSearch as $ddd) {
                                $oiProducts = $oiBRService->getPlans($pointOfSale->id, $ddd);
                                $products   = $products->merge($oiProducts);
                            }
                        }
                        $products = $products->unique('product');
                        break;
                    case Operations::SURF_PERNAMBUCANAS:
                        $pagtelService = resolve(PagtelService::class);
                        $pagtelPlans   = $pagtelService->activationPlans()->getAdapted();
                        $adapted       = PagtelPlansTriangulationAdapter::adapt(
                            data_get($pagtelPlans, 'plans', [])
                        );
                        $products      = $products->merge($adapted);
                        break;
                    default:
                        break;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
        return $products;
    }

    private static function adaptPromotions()
    {
        return ClaroPromotionsService::getPromotions();
    }

    private static function selectCorrectVivoIntegration(User $user, string $network)
    {
        $serviceOptions = ServiceOptionsFilter::make($user, [
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::VIVO,
            'operation' => Operations::VIVO_CONTROLE_CARTAO
        ])->verifyM4uTradeUp()->filter();

        if (in_array(ServiceOptionsFilter::VIVO_CONTROLE_CARTAO_M4U, $serviceOptions, true)) {
            return VivoM4uControleCartao::getControleCartaoToTriangulation();
        }

        $vivoBRService = resolve(VivoBRService::class);
        return $vivoBRService->getProducts(['network' => $network]);
    }
}
