<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class ProductNotFoundException extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.third_party.default');
    }

    public function getShortMessage()
    {
        return 'ProductNotFoundInPartner';
    }

    public function getDescription()
    {
        return trans('exceptions.third_party.default');
    }
}
