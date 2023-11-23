<?php


namespace Outsourced\ViaVarejo\Enumerators;

use TradeAppOne\Domain\Enumerators\Modes;

final class ViaVarejoModes
{
    public const MP = 'MP';
    public const NH = 'NH';
    public const PO = 'PO';

    public const MODE = [
        Modes::MIGRATION => self::MP,
        Modes::ACTIVATION => self::NH,
        Modes::PORTABILITY => self::PO
    ];

    public static function get(string $mode): string
    {
        return self::MODE[$mode] ?? '';
    }
}
