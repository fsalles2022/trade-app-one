<?php

namespace TradeAppOne\Domain\Components\Helpers;

final class MoneyHelper
{
    public static function formatMoney(float $value, int $decimalPlaces = 2, string $language = 'pt_BR')
    {
        $formatter = numfmt_create($language, $decimalPlaces);
        $formatted = numfmt_format($formatter, $value);

        return self::removeCharacterNoBreakSpace($formatted);
    }

    public static function removeCharacterNoBreakSpace($value)
    {
        return preg_replace("~\x{00a0}~siu", " ", $value);
    }

    public static function realToCents(float $price): string
    {
        return bcmul($price, 100, 0);
    }
    
    public static function formatCentsToReal(int $cents): string
    {
        if ($cents === 0) {
            return (string) $cents;
        }

        $real = ($cents / 100);

        return number_format($real, 2);
    }
}
