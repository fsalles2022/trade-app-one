<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRBearerSergeantFailed extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('timBR::messages.authentication.bearer_not_found');
    }

    public function getShortMessage()
    {
        return 'TimBRBearerNotFound';
    }

    public function getDescription()
    {
        return trans('timBR::exceptions.authentication.bearer_not_found');
    }
}
