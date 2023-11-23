<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\CustomRuleExceptions;

class ErrorSendingEmailException extends CustomRuleExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.error_sending_email.message');
    }

    public function getShortMessage()
    {
        return 'ErrorSendingEmailException';
    }

    public function getDescription()
    {
        return trans('exceptions.error_sending_email.message');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
