<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class ServiceNotFoundException extends BusinessRuleExceptions
{
    public function getShortMessage()
    {
        return 'NetworkNotFoundException';
    }

    public function getDescription()
    {
        return trans('exception.service.not_found');
    }

    public function getHelp()
    {
        return trans('help.network.not_found');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_FOUND;
    }
}
