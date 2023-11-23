<?php

namespace ClaroBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class PromotionNotFound extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.third_party.default');
    }

    public function getShortMessage()
    {
        return 'PromotionNotFound';
    }

    public function getDescription()
    {
        return '';
    }
}
