<?php


namespace VivoTradeUp\Repositories;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Plan;

class VivoM4uControleCartao
{
    private static $defaultAreaCode = 11;
    private static $plans           = [
        [
            'id' => 1902,
            'nome' => 'Vivo Controle Cartão - 8GB',
            'slug' => 'vivo_ctrl_cartao_8gb_2',
            'ddd' => 11,
            'valor' => 46.99,
            'tipo' => 'CONTROLE_CARTAO',
            'tipoFaturas' => [
                'CARTAO_CREDITO'
            ]
        ],
        [
            'id' => 1903,
            'nome' => 'Vivo Controle Cartão - 7GB',
            'slug' => 'vivo_ctrl_cartao_7gb_4',
            'ddd' => 11,
            'valor' => 34.99,
            'tipo' => 'CONTROLE_CARTAO',
            'tipoFaturas' => [
                'CARTAO_CREDITO'
            ]
        ],
    ];

    private static $plansForRiachuelo = [
        [
            'id' => 1902,
            'nome' => 'Vivo Controle Cartão - 8GB',
            'slug' => 'vivo_ctrl_cartao_8gb_2',
            'ddd' => 11,
            'valor' => 46.99,
            'tipo' => 'CONTROLE_CARTAO',
            'tipoFaturas' => [
                'CARTAO_CREDITO'
            ]
        ],
    ];

    private static $plansForCasaEVideo = [
        [
            'id' => 1902,
            'nome' => 'Vivo Controle Cartão - 8GB',
            'slug' => 'vivo_ctrl_cartao_8gb_2',
            'ddd' => 11,
            'valor' => 46.99,
            'tipo' => 'CONTROLE_CARTAO',
            'tipoFaturas' => [
                'CARTAO_CREDITO'
            ]
        ],
    ];

    public static function getPlansByNetwork(string $networkSlug): array
    {
        if ($networkSlug === NetworkEnum::RIACHUELO) {
            return self::$plansForRiachuelo;
        }

        if ($networkSlug === NetworkEnum::CASAEVIDEO) {
            return self::$plansForCasaEVideo;
        }

        return self::$plans;
    }

    public static function getControleCartaoM4u(string $networkSlug, ?int $areaCode = 11): array
    {
        $plans = self::getPlansByNetwork($networkSlug);

        if ($areaCode !== self::$defaultAreaCode) {
            foreach ($plans as $key => $plan) {
                $plans[$key]['ddd'] = $areaCode;
            }
        }
        return $plans;
    }

    public static function getControleCartaoToTriangulation(): Collection
    {
        $plans             = self::$plans;
        $collectionOfPlans = collect([]);
        foreach ($plans as $plan) {
            $planAdapted               = new Plan(
                data_get($plan, 'id'),
                data_get($plan, 'nome'),
                data_get($plan, 'valor'),
                $plan
            );
            $planAdapted->operation    = data_get($plan, 'tipo');
            $planAdapted->operator     = Operations::VIVO;
            $planAdapted->invoiceTypes = data_get($plan, 'tipoFaturas');
            $planAdapted->areaCode     = data_get($plan, 'ddd');
            $planAdapted->dependents   = 0;

            if ($planAdapted instanceof Plan) {
                $collectionOfPlans->push($planAdapted);
            }
        }
        return $collectionOfPlans;
    }
}
