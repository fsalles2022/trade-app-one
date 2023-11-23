<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class M4uAdapterRequestException extends ThirdPartyExceptions
{
    public function __construct(string $message = "")
    {
        $this->message = trans('timBR::exceptions.m4u.adapter');
    }

    public function getShortMessage()
    {
        return 'M4uAdapterRequestException';
    }

    public function getDescription()
    {
        return '';
    }
}
