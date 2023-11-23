<?php

namespace OiBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class OiBRStatusUnreach extends ThirdPartyExceptions
{
    public function getShortMessage()
    {
        return 'OiBRStatusUnreach';
    }

    public function getDescription()
    {
        return trans('oiBR::exceptions.status_unreach');
    }
}
