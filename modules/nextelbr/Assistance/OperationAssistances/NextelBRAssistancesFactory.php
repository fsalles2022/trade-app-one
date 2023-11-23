<?php

namespace NextelBR\Assistance\OperationAssistances;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\AssistanceBehavior;

class NextelBRAssistancesFactory
{
    private static $assistances = [
        Operations::NEXTEL_CONTROLE_BOLETO => NextelBRControleBoletoAssistance::class,
        Operations::NEXTEL_CONTROLE_CARTAO => NextelBRControleCartaoAssistance::class
    ];

    public static function make($operation): AssistanceBehavior
    {
        return resolve(self::$assistances[$operation]);
    }
}
