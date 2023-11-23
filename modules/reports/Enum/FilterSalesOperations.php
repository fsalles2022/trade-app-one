<?php

namespace Reports\Enum;

use TradeAppOne\Domain\Enumerators\Operations;

final class FilterSalesOperations
{
    const CLARO = [
        Operations::CLARO_PRE,
        Operations::CLARO_CONTROLE_BOLETO,
        Operations::CLARO_CONTROLE_FACIL,
        Operations::CLARO_POS,
        Operations::CLARO_VOZ_DADOS,
        Operations::CLARO_CONTROLE,
        Operations::CLARO_DADOS
    ];

    const OI = [
        Operations::OI_CONTROLE_CARTAO,
        Operations::OI_CONTROLE_BOLETO
    ];

    const VIVO = [
        Operations::VIVO_CONTROLE,
        Operations::VIVO_PRE,
        Operations::VIVO_CONTROLE_CARTAO,
        Operations::VIVO_POS_PAGO,
        Operations::VIVO_INTERNET_MOVEL_POS
    ];

    const TIM = [
        Operations::TIM_CONTROLE_FATURA,
        Operations::TIM_EXPRESS,
        Operations::TIM_PRE_PAGO
    ];
}
