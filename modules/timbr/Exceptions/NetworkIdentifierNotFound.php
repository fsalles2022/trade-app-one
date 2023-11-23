<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class NetworkIdentifierNotFound extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('timBR::exceptions.identifiers.network_not_found');
    }

    public function getShortMessage()
    {
        return 'NetworkTIMIdentifierNotFound';
    }

    public function getDescription()
    {
        return trans('timBR::exceptions.identifiers.network_not_found');
    }
}
