<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class InvalidDateOfBirthException extends BusinessRuleExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.user.invalid_date_of_birth');
    }

    public function getShortMessage()
    {
        return 'InvalidDateOfBirthException';
    }

    public function getDescription()
    {
        return trans('exceptions.user.invalid_date_of_birth');
    }

    public function getHelp()
    {
        return trans('help.user.invalid_date_of_birth');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
