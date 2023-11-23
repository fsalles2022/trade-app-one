<?php

namespace TradeAppOne\Domain\Components\Helpers;

use Carbon\Carbon;

class Period
{
    public $finalDate;
    public $initialDate;

    public function __construct(?Carbon $initialDate, ?Carbon $finalDate)
    {
        $this->initialDate = $initialDate;
        $this->finalDate   = $finalDate;
    }

    public static function parseFromCommand($options, $format = 'Y-m-d-H-i')
    {
        if ($initialDate = data_get($options, 'initial-date')) {
            $initialDate = Carbon::createFromFormat($format, $initialDate);
        }
        if ($finalDate = data_get($options, 'final-date')) {
            $finalDate = Carbon::createFromFormat($format, $finalDate);
        }
        return new Period($initialDate, $finalDate);
    }

    public function toArray()
    {
        return array_filter(['initialDate' => $this->initialDate, 'finalDate' => $this->finalDate]);
    }
}
