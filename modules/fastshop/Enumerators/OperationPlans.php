<?php


namespace FastShop\Enumerators;

use TradeAppOne\Domain\Enumerators\Operations;

class OperationPlans
{
    public const PLANS_MAP = [
        SimulatorFilterOptions::PLAN_CONTROLE => [
            Operations::CLARO_CONTROLE_BOLETO,
            Operations::CLARO_CONTROLE_FACIL,
            Operations::NEXTEL_CONTROLE_CARTAO,
            Operations::NEXTEL_CONTROLE_BOLETO,
            Operations::OI_CONTROLE_BOLETO,
            Operations::OI_CONTROLE_CARTAO,
            Operations::TIM_CONTROLE_FATURA,
            Operations::VIVO_CONTROLE_CARTAO,
            Operations::VIVO_CONTROLE
        ],
        SimulatorFilterOptions::PLAN_PRE_PAGO => [
            Operations::CLARO_PRE,
            Operations::TIM_PRE_PAGO,
            Operations::VIVO_PRE
        ],
        SimulatorFilterOptions::PLAN_POS_PAGO => [
            Operations::CLARO_POS,
            Operations::VIVO_POS_PAGO,
            Operations::VIVO_INTERNET_MOVEL_POS
        ]
    ];
}
