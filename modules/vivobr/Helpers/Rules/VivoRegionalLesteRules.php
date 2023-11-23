<?php

namespace VivoBR\Helpers\Rules;

use Illuminate\Support\Collection;
use VivoBR\Helpers\ListOfVivoPlans;

class VivoRegionalLesteRules implements BaseVivoFilterRule
{
    private const REGIONAL_LESTE = [
        'products' => [
            ListOfVivoPlans::VIVO_CONTROLE_DIGITAL_MENSAL_3_5_GB,
            ListOfVivoPlans::VIVO_CONTROLE_DIGITAL_MENSAL_4GB,
            ListOfVivoPlans::VIVO_CONTROLE_DIGITAL_MENSAL_5GB,
            ListOfVivoPlans::VIVO_POS_MENSAL_7GB,
            ListOfVivoPlans::VIVO_POS_MENSAL_8GB_E_8GB,
            ListOfVivoPlans::VIVO_POS_MENSAL_12GB_E_12GB,
            ListOfVivoPlans::VIVO_FAMILIA_MENSAL_30GB_E_30GB,
            ListOfVivoPlans::VIVO_FAMILIA_MENSAL_40GB_E_40GB,
            ListOfVivoPlans::VIVO_FAMILIA_MENSAL_50GB_E_50GB,
            ListOfVivoPlans::VIVO_FAMILIA_MENSAL_70GB_E_70GB,
            ListOfVivoPlans::VIVO_CONTROLE_DIGITAL_3_5GB,
            ListOfVivoPlans::VIVO_CONTROLE_DIGITAL_4_5GB,
            ListOfVivoPlans::VIVO_CONTROLE_DIGITAL_6_5GB,
            ListOfVivoPlans::VIVO_CONTROLE_PASS_DIGITAL_5GB,
            ListOfVivoPlans::VIVO_POS_MENSAL_8GB,
            ListOfVivoPlans::VIVO_POS_MENSAL_10GB,
            ListOfVivoPlans::VIVO_POS_MENSAL_25GB,
            ListOfVivoPlans::VIVO_POS_MENSAL_25GB_RAPPI,
            ListOfVivoPlans::VIVO_POS_MENSAL_25GB_SPOTIFY,
            ListOfVivoPlans::VIVO_POS_MENSAL_25GB_NETFLIX,
            ListOfVivoPlans::VIVO_POS_MENSAL_35GB,
            ListOfVivoPlans::VIVO_POS_MENSAL_35GB_PREMIERE,
            ListOfVivoPlans::VIVO_FAMILIA_MENSAL_60GB,
            ListOfVivoPlans::VIVO_FAMILIA_MENSAL_80GB,
            ListOfVivoPlans::VIVO_FAMILIA_MENSAL_100GB,
        ],
    ];

    public function hasToFilter(string $cnpj, string $network): bool
    {
        $pointsOfSale = json_decode(file_get_contents(__DIR__ . '/../VivoRegionalLeste/points_of_sale.json'), true);
        return in_array($cnpj, $pointsOfSale, true);
    }

    public function filter(Collection $plans): Collection
    {
        return $plans->whereNotIn('product', self::REGIONAL_LESTE['products']);
    }
}
