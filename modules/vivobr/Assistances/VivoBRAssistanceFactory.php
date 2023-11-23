<?php

namespace VivoBR\Assistances;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\AssistanceBehavior;

class VivoBRAssistanceFactory
{
    protected static $operations = [
        Operations::VIVO_CONTROLE           => VivoBRControleAssistance::class,
        Operations::VIVO_CONTROLE_CARTAO    => VivoBRControleCartaoAssistance::class,
        Operations::VIVO_PRE                => VivoBRPreAssistance::class,
        Operations::VIVO_POS_PAGO           => VivoBRPosPagoAssistance::class,
        Operations::VIVO_INTERNET_MOVEL_POS => VivoBRInternetMovelPosAssistance::class
    ];

    public static function make(string $operation): AssistanceBehavior
    {
        return resolve(self::$operations[$operation]);
    }
}
