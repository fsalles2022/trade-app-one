<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class DateExceptions
{
    const FORMAT_INCORRECT = "dateIncorrect";

    public static function dateIncorrect(string $date)
    {
        return new BuildExceptions([
            'shortMessage' => self::FORMAT_INCORRECT,
            'message' => trans('exceptions.date.' . self::FORMAT_INCORRECT, ['date' => $date]),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
