<?php

namespace TradeAppOne\Domain\Components\Helpers;

use DateTime;
use DateTimeZone;
use MongoDB\BSON\UTCDateTime;

class MongoDateHelper
{
    public static function utcToDateTime(string $year, string $moth, string $day): DateTime
    {
        $dateTime = new Datetime("{$year}-{$moth}-{$day}");
        return $dateTime;
    }

    public static function dateTimeToUtc(DateTime $dateTime): UTCDateTime
    {
        return new UTCDateTime($dateTime);
    }

    public static function now(): DateTime
    {
        return new \DateTime();
    }

    public static function millisecondsToFormat(UTCDateTime $dateTime, string $format = DATE_ATOM): string
    {
        return $dateTime->toDateTime()->format($format);
    }
}
