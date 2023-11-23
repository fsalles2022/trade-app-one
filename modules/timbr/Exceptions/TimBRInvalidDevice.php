<?php

declare(strict_types=1);

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRInvalidDevice extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('timBR::exceptions.TimBRInvalidDevice.message');
    }

    public function getShortMessage()
    {
        return 'TimBRInvalidDevice';
    }

    public function getDescription()
    {
        return trans('timBR::exceptions.TimBRInvalidDevice.message');
        ;
    }
}
