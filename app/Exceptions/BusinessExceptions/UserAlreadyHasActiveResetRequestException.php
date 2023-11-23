<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;
use Throwable;

class UserAlreadyHasActiveResetRequestException extends BusinessRuleExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.user.already_has_active_reset_request');
    }

    public function getShortMessage()
    {
        return 'UserAlreadyHasActiveResetRequestException';
    }

    public function getDescription()
    {
        return trans('exception.user.already_has_active_reset_request');
    }

    public function getHelp()
    {
        return trans('help.user.already_has_active_reset_request');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
