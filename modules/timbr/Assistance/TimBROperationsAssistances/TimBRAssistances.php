<?php

namespace TimBR\Assistance\TimBROperationsAssistances;

use TradeAppOne\Domain\Enumerators\Operations;

class TimBRAssistances
{
    /** @var string[] */
    private static $assistances = [
        Operations::TIM_CONTROLE_FATURA         => TimBRControleFaturaAssistance::class,
        Operations::TIM_EXPRESS                 => TimBRControleExpressAssistance::class,
        Operations::TIM_PRE_PAGO                => TimBRPrePagoAssistance::class,
        Operations::TIM_CONTROLE_FLEX           => TimBRControleFlexAssistance::class,
        Operations::TIM_BLACK                   => TimBRBlackAssistance::class,
        Operations::TIM_BLACK_EXPRESS           => TimBRBlackExpressAssistance::class,
        Operations::TIM_BLACK_MULTI             => TimBRBlackMultiAssistance::class,
        Operations::TIM_BLACK_MULTI_DEPENDENT   => TimBRBlackMultiDependentAssistance::class,
    ];

    public static function make(string $strategy): TimBROperationsAssistanceInterface
    {
        return resolve(self::$assistances[$strategy]);
    }
}
