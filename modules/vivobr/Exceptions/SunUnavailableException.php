<?php

namespace VivoBR\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class SunUnavailableException extends ThirdPartyExceptions
{
    public function __construct(string $message = "")
    {
        $this->message = $message;
    }

    public function getShortMessage()
    {
        return 'SunUnavailable';
    }

    public function getDescription()
    {
        return trans('exceptions.third_party_unavailable', ['service' => 'Vivo']);
    }

    public function getHelp()
    {
        return trans('help.third_party_unavailable');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_MISDIRECTED_REQUEST;
    }

    public function getTransportedMessage()
    {
        return $this->transportedMessage;
    }

    public function report()
    {
        Log::debug('third-party-api-sun-get', ['url' => 'offline', 'exception' => $this->getMessage()]);
    }
}
