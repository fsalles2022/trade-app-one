<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRAuthenticationInvalidCookies extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('timBR::exceptions::TimBRAuthenticationInvalidCookies.message');
    }

    public function getShortMessage()
    {
        return 'TimBRAuthenticationInvalidCookies';
    }

    public function getDescription()
    {
        return 'TimBRAuthenticationInvalidCookies';
    }
}
