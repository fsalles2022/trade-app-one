<?php


namespace Outsourced\ViaVarejo\Enumerators;

use \TradeAppOne\Domain\Enumerators\Operations;

final class ViaVarejoOperators
{
    private const OTHER        = 'OTHER';
    private const TWO          = 2;
    private const THREE        = 3;
    private const FIVE         = 5;
    private const SEVEN        = 7;
    private const SIXTEEN      = 16;
    private const TWENTY_THREE = 23;

    public const LINE_ACTIVATION = [
        Operations::CLARO => self::TWO,
        Operations::TIM => self::SEVEN,
        Operations::OI => self::FIVE,
        Operations::NEXTEL => self::SIXTEEN,
        Operations::NET => self::TWENTY_THREE,
        Operations::VIVO => self::THREE,
        Operations::CTBC => self::TWENTY_THREE,
        self::OTHER => self::TWENTY_THREE,
    ];

    public static function get(string $operator): ?int
    {
        return self::LINE_ACTIVATION[$operator] ?? null;
    }

    public static function getPortabilityOperator(array $fromOperator): ?int
    {
        $label = data_get($fromOperator, 'label', 'OTHER');
        return  data_get(self::LINE_ACTIVATION, $label) ?? self::TWENTY_THREE;
    }
}
