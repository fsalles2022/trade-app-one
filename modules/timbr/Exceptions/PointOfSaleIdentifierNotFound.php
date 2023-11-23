<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class PointOfSaleIdentifierNotFound extends ThirdPartyExceptions
{
    public function __construct(string $message = "")
    {
        $this->description = $message;
        $this->message     = trans('timBR::exceptions.identifiers.point_of_sale_not_found');
    }

    public function getShortMessage()
    {
        return 'PointOfSaleTIMIdentifierNotFound';
    }

    public function getDescription()
    {
        return trans('timBR::exceptions.identifiers.point_of_sale_not_found');
    }
}
