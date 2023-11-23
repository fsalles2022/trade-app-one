<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class ServiceInvalidException extends BusinessRuleExceptions
{
    public function getShortMessage()
    {
        return 'ServiceInvalidException';
    }

    public function getDescription()
    {
        return trans('exception.general.service_invalid');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
