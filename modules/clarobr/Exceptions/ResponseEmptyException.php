<?php

namespace ClaroBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class ResponseEmptyException extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('siv::exceptions.ResponseEmptyException.message');
    }

    public function getShortMessage()
    {
        return 'ResponseEmptyException';
    }

    public function getDescription()
    {
        return trans('siv::exceptions.ResponseEmptyException.message');
    }
}
