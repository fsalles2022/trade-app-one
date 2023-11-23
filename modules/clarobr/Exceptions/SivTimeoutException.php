<?php

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class SivTimeoutException extends ThirdPartyExceptions
{
    public function __construct(string $message = "")
    {
        $this->transportedMessage = $message;
    }

    public function getDescription()
    {
        return trans('exceptions.siv_timeout');
    }

    public function getShortMessage()
    {
        return 'SivTimeout';
    }

    public function getHelp()
    {
        return trans('help.siv_timeout');
    }

    public function getTransportedMessage()
    {
        return $this->transportedMessage;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_GATEWAY_TIMEOUT;
    }
}
