<?php

namespace VivoBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class SunNoAccessException extends ThirdPartyExceptions
{
    public const NOT_REGISTERED_IN_PARTNER =  'NotRegisteredInPartner';

    public function __construct()
    {
        $this->message = $this->getDescription();
        parent::__construct();
    }

    public function getShortMessage()
    {
        return self::NOT_REGISTERED_IN_PARTNER;
    }

    public function getDescription()
    {
        return trans('sun::exceptions.'. self::NOT_REGISTERED_IN_PARTNER);
    }
}
