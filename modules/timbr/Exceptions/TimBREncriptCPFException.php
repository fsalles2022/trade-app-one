<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBREncriptCPFException extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('timBR::exceptions.TimBREncriptCPFException.message');
    }

    public function getShortMessage()
    {
        return 'TimBREncriptCPFException';
    }

    public function getDescription()
    {
        $this->message = trans('timBR::exceptions.TimBREncriptCPFException.description');
    }
}
