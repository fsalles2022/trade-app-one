<?php

namespace ClaroBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class ClaroBRProvisionalNumberException extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('siv::exceptions.ClaroBRProvisionalNumberException.message');
    }

    public function getShortMessage()
    {
        return 'ClaroBRProvisionalNumberException';
    }

    public function getDescription()
    {
        return '';
    }
}
