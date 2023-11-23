<?php

namespace Gateway\Components;

class Interest
{
    public const ONCE            = 0;
    public const TWO_TO_SIX      = 0;
    public const SEVEN_TO_TWELVE = 0;

    public const RATES = [
        1 => self::ONCE,
        2 => self::TWO_TO_SIX,
        3 => self::TWO_TO_SIX,
        4 => self::TWO_TO_SIX,
        5 => self::TWO_TO_SIX,
        6 => self::TWO_TO_SIX,
        7 => self::SEVEN_TO_TWELVE,
        8 => self::SEVEN_TO_TWELVE,
        9 => self::SEVEN_TO_TWELVE,
        10 => self::SEVEN_TO_TWELVE,
        11 => self::SEVEN_TO_TWELVE,
        12 => self::SEVEN_TO_TWELVE,
    ];

    public static function apply(float $price, int $times, bool $firstFree = false): float
    {
        if ($firstFree) {
            $price = self::remove($price, 1);
        }

        return round($price / (1 - self::RATES[$times]), 2);
    }

    public static function remove(float $price, int $times): float
    {
        return round($price * (1 - self::RATES[$times]), 2);
    }

    public static function all(float $price, bool $firstFree = false): array
    {
        $installments = [];

        foreach (self::RATES as $times => $fee) {
            $priceWithInterest = $firstFree && $times === 1 ? $price : self::apply($price, $times);

            $installments[] = [
                'price' => round($priceWithInterest / $times, 2),
                'times' => $times,
                'interest' => $fee,
                'message' => $times === 1 & $firstFree ? 'Sem Juros' : ($fee === 0 ? 'Sem Juros' : 'Com Juros')
            ];
        }

        return $installments;
    }
}
