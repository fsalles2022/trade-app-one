<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRInvalidProduct extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('timBR::exceptions.TimBRInvalidProduct.message');
    }

    public function getShortMessage()
    {
        return 'TimBRInvalidProduct';
    }

    public function getDescription()
    {
        return trans('timBR::exceptions.TimBRInvalidProduct.message');
        ;
    }
}
