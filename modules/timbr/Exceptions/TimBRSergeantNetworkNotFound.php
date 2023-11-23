<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRSergeantNetworkNotFound extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('timBR::exceptions.identifiers.sergeant_not_found');
    }

    public function getShortMessage()
    {
        return 'TimBRSergeantNetworkNotFound';
    }

    public function getDescription()
    {
        return trans('timBR::exceptions.identifiers.sergeant_not_found');
    }
}
