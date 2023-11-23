<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;
use Throwable;

class UserNotFoundException extends BusinessRuleExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.user.not_found');
    }

    public function getShortMessage()
    {
        return 'UserNotFoundException';
    }

    public function getDescription()
    {
        return trans('exception.user.not_found');
    }

    public function getHelp()
    {
        return trans('help.user.not_found');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_FOUND;
    }
}
