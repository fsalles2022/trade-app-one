<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class EmptyPlansException extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.empty_plans.message');
    }

    public function getShortMessage()
    {
        return 'EmptyPlansException';
    }

    public function getDescription()
    {
        return '';
    }
}
