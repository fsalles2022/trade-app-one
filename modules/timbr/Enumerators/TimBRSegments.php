<?php

namespace TimBR\Enumerators;

use TradeAppOne\Domain\Enumerators\Operations;

final class TimBRSegments
{
    public const CONTROLE      = 'CONTROLE';
    public const EXPRESS       = 'EXPRESS';
    public const PRE_PAGO      = 'PRE_PAGO';
    public const POS_PAGO      = 'POS_PAGO';
    public const CONTROLE_FLEX = 'CONTROLE_FLEX';
    public const POS_EXPRESS   = 'POS_EXPRESS';
    public const DIGITALPOS    = 'DIGITALPOS';

    public const TRANSLATE = [
        Operations::TIM_CONTROLE_FATURA         => self::CONTROLE,
        Operations::TIM_EXPRESS                 => self::EXPRESS,
        Operations::TIM_PRE_PAGO                => self::PRE_PAGO,
        Operations::TIM_POS_PAGO                => self::POS_PAGO,
        Operations::TIM_CONTROLE_FLEX           => self::CONTROLE_FLEX,
        Operations::TIM_BLACK                   => self::POS_PAGO,
        Operations::TIM_BLACK_EXPRESS           => self::POS_EXPRESS,
        Operations::TIM_BLACK_MULTI             => self::DIGITALPOS,
        Operations::TIM_BLACK_MULTI_DEPENDENT   => self::DIGITALPOS,
    ];
}
