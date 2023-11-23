<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRCepNotFound extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('timBR::exceptions.cep.not_found');
    }

    public function getShortMessage()
    {
        return 'TimBRCepNotFound';
    }

    public function getDescription()
    {
        return trans('timBR::exceptions.cep.not_found');
    }
}
