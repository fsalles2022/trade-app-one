<?php

namespace ClaroBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class PlansNotFoundException extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('siv::exceptions.PlansNotFoundException.message');
    }

    public function getShortMessage()
    {
        return 'PlansNotFoundException';
    }

    public function getDescription()
    {
        return '';
    }
}
