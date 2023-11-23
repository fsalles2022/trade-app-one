<?php

namespace Reports\Enum;

use TradeAppOne\Domain\Enumerators\Operations;

class PreControlePosLineActivationOperations
{
    const PRE = [
        Operations::VIVO_PRE,
        Operations::CLARO_PRE
    ];

    const CONTROLE = [
        Operations::VIVO_CONTROLE_CARTAO,
        Operations::VIVO_CONTROLE,
        Operations::CLARO_CONTROLE_FACIL,
        Operations::CLARO_CONTROLE_BOLETO,
        Operations::CLARO_DADOS,
        Operations::CLARO_VOZ_DADOS,
        Operations::CLARO_CONTROLE,
        Operations::CLARO_CONTROLE_BOLETO,
        Operations::TIM_EXPRESS,
        Operations::TIM_CONTROLE_FATURA,
        Operations::OI_CONTROLE_CARTAO,
        Operations::OI_CONTROLE_BOLETO,
        Operations::NEXTEL_CONTROLE_BOLETO,
        Operations::NEXTEL_CONTROLE_CARTAO
    ];

    const POS = [
        Operations::VIVO_POS_PAGO,
        Operations::CLARO_POS,
    ];
}
