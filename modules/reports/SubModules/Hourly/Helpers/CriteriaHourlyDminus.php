<?php

namespace Reports\SubModules\Hourly\Helpers;

use Carbon\Carbon;

/**
 * @property Carbon day
 * @property Carbon dMinusEnd
 * @property Carbon dMinusStart
 * @property int strategy
 */
class CriteriaHourlyDminus
{
    const D1 = 1;
    const D3 = 3;

    /**
     * @var Carbon
     */
    public $day;
    public $dMinusEnd;
    public $dMinusStart;
    public $strategy;

    public function __construct(Carbon $day)
    {
        $this->day       = $day;
        $this->dMinusEnd = $day->copy()->subDays(1)->endOfDay();
        if (self::strategy($day) == self::D3) {
            $this->dMinusStart = $this->dMinusEnd->copy()->subDays(self::strategy($day))->startOfDay();
        } else {
            $this->dMinusStart = $day->copy()->subDays(1)->startOfDay();
        }
        $this->strategy = self::strategy($day);
    }

    public static function strategy(Carbon $day): int
    {
        if ($day->isMonday()) {
            return self::D3;
        }
        return self::D1;
    }

    public static function period(Carbon $day): array
    {
        $end = $day->copy()->subDay()->endOfDay();
        if (self::strategy($day) == self::D3) {
            $start = $end->copy()->startOfDay()->subDays(self::strategy($day) - 1);
        } else {
            $start = $end->copy()->subDays(1)->startOfDay();
        }

        return [$start, $end];
    }
}
