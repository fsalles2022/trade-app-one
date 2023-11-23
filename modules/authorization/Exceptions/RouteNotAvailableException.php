<?php

namespace Authorization\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\CustomRuleExceptions;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class RouteNotAvailableException extends CustomRuleExceptions
{
    const KEY = 'RouteNotAvailableException';

    public function __construct()
    {
        $this->message = trans('authorization::exceptions.'.  self::KEY . ".message");
    }

    public function getShortMessage()
    {
        return self::KEY;
    }

    public function getDescription()
    {
        return trans('authorization::exceptions.'. self::KEY . '.description');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_FORBIDDEN;
    }
}
