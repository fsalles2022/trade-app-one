<?php

namespace Reports\Goals\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class GoalsExceptions
{
    const MONTH_GOALS_NOT_FOUND = "MonthGoalsNotFound";

    public static function monthGoalsNotFound()
    {
        return new BuildExceptions([
            'shortMessage' => self::MONTH_GOALS_NOT_FOUND,
            'message' => trans('goals::exceptions.goal.' . self::MONTH_GOALS_NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }
}
