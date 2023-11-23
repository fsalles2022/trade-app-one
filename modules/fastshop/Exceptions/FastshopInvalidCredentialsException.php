<?php

namespace FastShop\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class FastshopInvalidCredentialsException extends ThirdPartyExceptions
{
    public function __construct($message = null)
    {
        $this->message            = trans('fastshop::exceptions.credentials.invalid');
        $this->transportedMessage = $message;
    }

    public function getDescription()
    {
        return trans('fastshop::exceptions.credentials.invalid');
    }

    public function getShortMessage()
    {
        return 'FastShopApiInvalidCredentials';
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
        return Response::HTTP_PRECONDITION_FAILED;
    }
}
