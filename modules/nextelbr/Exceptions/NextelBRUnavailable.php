<?php

namespace NextelBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class NextelBRUnavailable extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('nextelBR::exceptions.NextelBRUnavailable.message');
    }

    public function getShortMessage()
    {
        return 'NextelBRUnavailable';
    }

    public function getDescription()
    {
        return trans('nextelBR::exceptions.NextelBRUnavailable.description');
    }
}
