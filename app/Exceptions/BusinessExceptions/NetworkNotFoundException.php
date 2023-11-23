<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class NetworkNotFoundException extends BusinessRuleExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.network.not_found');
    }
    
    public function getShortMessage()
    {
        return 'NetworkNotFoundException';
    }

    public function getDescription()
    {
        return trans('exceptions.network.not_found');
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
