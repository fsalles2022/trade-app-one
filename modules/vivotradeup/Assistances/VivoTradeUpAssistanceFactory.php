<?php

namespace VivoTradeUp\Assistances;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\AssistanceBehavior;

class VivoTradeUpAssistanceFactory
{
    protected static $operations = [
        Operations::VIVO_CONTROLE_CARTAO    => VivoTradeUpControleCartaoAssistance::class,
    ];

    public static function make(string $operation): AssistanceBehavior
    {
        return resolve(self::$operations[$operation]);
    }
}
