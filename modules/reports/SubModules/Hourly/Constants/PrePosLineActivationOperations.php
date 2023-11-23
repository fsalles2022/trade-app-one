<?php

namespace Reports\SubModules\Hourly\Constants;

use TradeAppOne\Domain\Enumerators\Operations;

final class PrePosLineActivationOperations
{
    const PRE = [
        Operations::VIVO_PRE,
        Operations::CLARO_PRE,
        Operations::TIM_PRE_PAGO
    ];

    const POS = [
        Operations::VIVO_POS_PAGO,
        Operations::VIVO_CONTROLE_CARTAO,
        Operations::VIVO_CONTROLE,
        Operations::CLARO_POS,
        Operations::CLARO_CONTROLE_FACIL,
        Operations::CLARO_CONTROLE_BOLETO,
        Operations::TIM_EXPRESS,
        Operations::TIM_CONTROLE_FATURA,
        Operations::OI_CONTROLE_CARTAO,
        Operations::OI_CONTROLE_BOLETO,
        Operations::NEXTEL_CONTROLE_BOLETO,
        Operations::NEXTEL_CONTROLE_CARTAO,
        Operations::CLARO_BANDA_LARGA,
        Operations::CLARO_VOZ_DADOS,
        Operations::CLARO_CONTROLE,
        Operations::CLARO_DADOS
    ];
}
