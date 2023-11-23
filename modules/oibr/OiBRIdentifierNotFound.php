<?php

namespace OiBR;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class OiBRIdentifierNotFound extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('oiBR::exceptions.identifiers.not_found');
    }

    public function getShortMessage()
    {
        return 'OiBRIdentifierNotFound';
    }

    public function getDescription()
    {
        return trans('exceptions.third_party.default');
    }
}
