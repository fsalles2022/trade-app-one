<?php

namespace TradeAppOne\Domain\Components\Helpers;

use Carbon\Carbon;
use TradeAppOne\Exceptions\SystemExceptions\DateExceptions;

final class DateConvertHelper
{
    public static function convertToStringFormat(?string $date, string $format): ?string
    {
        if ($date) {
            $date = str_replace('/', '-', $date);
            return date($format, strtotime($date));
        }
        return null;
    }

    public static function convertMonthYearToYearMonth(string $date)
    {
        return Carbon::createFromFormat('m/y', $date)->format('Ym');
    }

    public static function validateAndConvertOfDMY($date, $format = 'Y-m-d')
    {
        $dateInstance = \DateTime::createFromFormat('d/m/Y', $date);

        if ($dateInstance) {
            return $dateInstance->format($format);
        }

        throw DateExceptions::dateIncorrect($date);
    }
}
