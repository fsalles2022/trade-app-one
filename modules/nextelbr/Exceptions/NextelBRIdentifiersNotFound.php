<?php

namespace NextelBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class NextelBRIdentifiersNotFound extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('nextelBR::exceptions.NextelBRIdentifiersNotFound.message');
    }

    public function getShortMessage()
    {
        return 'NextelBRIdentifiersNotFound';
    }

    public function getDescription()
    {
        return;
    }
}
