<?php

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class SivUnavailableException extends ThirdPartyExceptions
{
    public function __construct(string $message = "")
    {
        $this->transportedMessage = $message;
    }

    public function getDescription()
    {
        return trans('exceptions.third_party_unavailable', ['service' => 'Claro']);
    }

    public function getShortMessage()
    {
        return 'SivUnavailable';
    }

    public function getHelp()
    {
        return trans('help.third_party_unavailable');
    }

    public function getTransportedMessage()
    {
        return $this->transportedMessage;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_MISDIRECTED_REQUEST;
    }

    public function report()
    {
        Log::debug('third-party-api-siv-get', ['url' => 'offline']);
    }
}
