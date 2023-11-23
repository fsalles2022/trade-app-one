<?php

namespace NextelBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class EligibilityNotFound extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('nextelBR::exceptions.EligibilityNotFound.message');
    }

    public function getShortMessage()
    {
        return 'EligibilityNotFound';
    }

    public function getDescription()
    {
        return trans('nextelBR::exceptions.EligibilityNotFound.description');
    }
}
