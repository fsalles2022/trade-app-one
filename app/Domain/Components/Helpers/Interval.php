<?php

namespace TradeAppOne\Domain\Components\Helpers;

use Carbon\Carbon;
use TradeAppOne\Domain\Enumerators\Intervals;

class Interval
{
    public static function createFromString(string $interval) : Period
    {
        return self::getInterval($interval);
    }

    private static function getInterval(string $interval) :Period
    {
        switch ($interval) {
            case Intervals::DAY:
                $initialDate = Carbon::now()->startOfDay();
                $finalDate   = Carbon::now()->endOfDay();
                break;

            case Intervals::WEEK:
                $initialDate = Carbon::now()->startOfWeek();
                $finalDate   = Carbon::now()->endOfWeek();
                break;

            case Intervals::MONTH:
                $initialDate = Carbon::now()->startOfMonth();
                $finalDate   = Carbon::now()->endOfMonth();
                break;

            case Intervals::ALL:
                $initialDate = Carbon::create(2018, 06, 27)->startOfDay();
                $finalDate   = Carbon::now();
                break;

            default:
                $initialDate = null;
                $finalDate   = null;
                break;
        }

        return new Period($initialDate, $finalDate);
    }
}
