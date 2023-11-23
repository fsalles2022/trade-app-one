<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRAuthenticationFailed extends ThirdPartyExceptions
{
    public function __construct($message = '')
    {
        $this->transportedMessage = $message;
        $this->message            = trans('timBR::messages.authentication.failed');
    }

    public function getShortMessage()
    {
        return 'AuthenticationFailed';
    }

    public function getDescription()
    {
        return trans('timBR::exceptions.authentication.failed');
    }
}
