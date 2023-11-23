<?php

namespace ClaroBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class RebateNotFound extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('siv::exceptions.RebateNotFound.message');
    }

    public function getShortMessage()
    {
        return 'RebateNotFound';
    }

    public function getDescription()
    {
        return '';
    }
}
