<?php

namespace NextelBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class PlanNotEligible extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('nextelBR::exceptions.PlanNotEligible.message');
    }

    public function getShortMessage()
    {
        return 'PlanNotEligible';
    }

    public function getDescription()
    {
        return trans('nextelBR::exceptions.PlanNotEligible.description');
    }
}
