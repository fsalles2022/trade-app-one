<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRCodeGenerationFailed extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.third_party.default');
    }

    public function getShortMessage()
    {
        return 'TimBRCodeGenerationFailed';
    }

    public function getDescription()
    {
        return trans('timBR::exceptions.authentication.code_not_found');
    }
}
