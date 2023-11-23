<?php

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class NoAccessToSivException extends ThirdPartyExceptions
{
    public function __construct(string $message = "")
    {
        $this->transportedMessage = $message;
    }

    public function getDescription()
    {
        return trans('exceptions.no_access_to_siv');
    }

    public function getShortMessage()
    {
        return 'NoAccessToSiv';
    }

    public function getHelp()
    {
        return trans('help.no_access_to_siv');
    }

    public function getTransportedMessage()
    {
        return $this->transportedMessage;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_PRECONDITION_REQUIRED;
    }
}
