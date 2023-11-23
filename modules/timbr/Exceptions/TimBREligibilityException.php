<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBREligibilityException extends ThirdPartyExceptions
{
    public function __construct(string $description = '')
    {
        $this->description = $description;
        $this->message     = trans('timBR::exceptions.eligibility.request_adapter');
    }

    public function getShortMessage()
    {
        return 'TimBREligibilityException';
    }

    public function getDescription()
    {
        return $this->description;
    }
}
