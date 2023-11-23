<?php

namespace Reports\SubModules\Hourly\Constants;

use TradeAppOne\Domain\Enumerators\Operations;

final class OperationsByOperators
{
    const POS_PAGO = [
            Operations::CLARO  => [
                Operations::CLARO_POS,
                Operations::CLARO_CONTROLE_FACIL,
                Operations::CLARO_CONTROLE_BOLETO,
            ],
            Operations::OI     => [
                Operations::OI_CONTROLE_BOLETO,
                Operations::OI_CONTROLE_CARTAO,
            ],
            Operations::TIM    => [
                Operations::TIM_EXPRESS,
                Operations::TIM_CONTROLE_FATURA,
            ],
            Operations::NEXTEL => [
                Operations::NEXTEL_CONTROLE_CARTAO,
                Operations::NEXTEL_CONTROLE_BOLETO,
            ],
            Operations::VIVO   => [
                Operations::VIVO_POS_PAGO,
                Operations::VIVO_CONTROLE_CARTAO,
                Operations::VIVO_CONTROLE,
            ]
        ];

    const PRE_PAGO = [
            Operations::CLARO => [
                Operations::CLARO_PRE
            ],
            Operations::VIVO  => [
                Operations::VIVO_PRE
            ]
        ];

    const GROUP_OF_OPERATIONS = [
        self::POS_PAGO,
        self::PRE_PAGO,
    ];
}
