<?php

namespace TradeAppOne\Domain\Components\Elasticsearch;

use Carbon\Carbon;

class DatetimeConverter
{
    public static function toApplicationTimezone(?string $source): Carbon
    {
        $carbonInstance     = Carbon::parse($source);
        $carbonInstance->tz = config('app.timezone');
        return $carbonInstance;
    }

    public static function splitDateAndTime(
        ?string $source,
        ?string $formatDate = 'd/m/Y',
        ?string $formatTime = 'H:i'
    ): array {
        $carbonInstance     = Carbon::parse($source);
        $carbonInstance->tz = config('app.timezone');

        return [$carbonInstance->format($formatDate), $carbonInstance->format($formatTime)];
    }
}
