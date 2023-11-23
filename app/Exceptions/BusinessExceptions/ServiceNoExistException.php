<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class ServiceNoExistException extends BusinessRuleExceptions
{
    public function __construct(string $message = "")
    {
        $this->transportedMessage = $message;
    }

    public function getDescription()
    {
        return trans('exceptions.service_no_exists');
    }

    public function getShortMessage()
    {
        return 'ServiceNoExists';
    }

    public function getHelp()
    {
        return trans('help.service_no_exists');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_MISDIRECTED_REQUEST;
    }
}
