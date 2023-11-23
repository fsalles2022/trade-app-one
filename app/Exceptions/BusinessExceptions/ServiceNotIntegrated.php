<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class ServiceNotIntegrated extends BusinessRuleExceptions
{

    public function getShortMessage()
    {
        return 'ServiceNotIntegrated';
    }

    public function getDescription()
    {
        return trans('exceptions.service_non_integrated');
    }

    public function getHelp()
    {
        return trans('help.service_non_integrated');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
