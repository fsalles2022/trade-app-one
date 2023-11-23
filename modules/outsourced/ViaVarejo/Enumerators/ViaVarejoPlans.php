<?php


namespace Outsourced\ViaVarejo\Enumerators;

use ClaroBR\Enumerators\SivOperations;
use TradeAppOne\Domain\Enumerators\Operations;

final class ViaVarejoPlans
{
    public const ZERO   = 0;
    public const ONE    = 1;
    public const TWO    = 2;
    public const THREE  = 3;
    public const FOUR   = 4;
    public const FIVE   = 5;
    public const SEVEN  = 7;
    public const TWELVE = 12;

    private const YES = 'S';
    private const NO  = 'N';

    public const OPTIONS = [
        Operations::CLARO => [
            Operations::CLARO_BANDA_LARGA => self::TWELVE,
            Operations::CLARO_CONTROLE_BOLETO => self::FIVE,
            Operations::CLARO_CONTROLE_FACIL => self::THREE,
            Operations::CLARO_POS => self::FOUR,
            Operations::CLARO_PRE => self::TWO,
            Operations::CLARO_VOZ_DADOS => self::ONE,
            Operations::CLARO_CONTROLE => self::FIVE,
            Operations::CLARO_DADOS => self::ONE,

            Operations::CLARO_RESIDENCIAL => self::TWELVE,
            SivOperations::TELEVISAO => self::TWELVE,
            SivOperations::TELEVISAO_CLARO_TV => self::TWELVE,
            SivOperations::TELEVISAO_NET => self::TWELVE,
            SivOperations::CLARO_TV_PRE => self::TWELVE,
            SivOperations::BANDA_LARGA_CLARO_TV => self::TWELVE,
            SivOperations::BANDA_LARGA => self::TWELVE,
            SivOperations::BANDA_LARGA_NET => self::TWELVE,
            SivOperations::FIXO => self::TWELVE,
            SivOperations::FIXO_CLARO_TV => self::TWELVE,
            SivOperations::FIXO_NET => self::TWELVE,
            SivOperations::PONTO_ADICIONAL => self::TWELVE
        ],
        Operations::NEXTEL => [
            Operations::NEXTEL_CONTROLE_BOLETO => self::FIVE,
            Operations::NEXTEL_CONTROLE_CARTAO => self::THREE,
        ],
        Operations::VIVO => [
            Operations::VIVO_CONTROLE => self::FIVE,
            Operations::VIVO_CONTROLE_CARTAO => self::THREE,
            Operations::VIVO_POS_PAGO => self::FOUR,
            Operations::VIVO_PRE => self::TWO,
            Operations::VIVO_INTERNET_MOVEL_POS => self::ONE
        ],
        Operations::TIM => [
            Operations::TIM_CONTROLE_FATURA => self::FIVE,
            Operations::TIM_EXPRESS => self::THREE,
            Operations::TIM_PRE_PAGO => self::TWO
        ],
        Operations::OI => [
            Operations::OI_CONTROLE_BOLETO => self::FIVE,
            Operations::OI_CONTROLE_CARTAO => self::THREE
        ]
    ];

    public static function get(string $operator, string $operations): string
    {
        return self::OPTIONS[$operator][$operations] ?? '';
    }

    public static function getFidelity(?array $promotion = null): string
    {
        $label = data_get($promotion, 'label');
        return preg_match('/fidel*/i', $label)
            ? self::YES
            : self::NO;
    }
}
