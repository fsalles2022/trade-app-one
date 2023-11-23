<?php

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class SivInvalidCredentialsException extends ThirdPartyExceptions
{
    public function __construct($message)
    {
        $this->message            = trans('siv::exceptions.credentials.invalid');
        $this->transportedMessage = $message;
    }

    public function getDescription()
    {
        return trans('exceptions.siv_invalid_credentials');
    }

    public function getShortMessage()
    {
        return 'SivInvalidCredentials';
    }

    public function getHelp()
    {
        return trans('help.siv_invalid_credentials');
    }

    public function getTransportedMessage()
    {
        return $this->transportedMessage;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_PRECONDITION_FAILED;
    }
}
