<?php

namespace TimBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BusinessExceptions\BusinessRuleExceptions;

class EligibilityNotFound extends BusinessRuleExceptions
{
    public function __construct()
    {
        $this->message = trans('timBR::exceptions.eligibility.not_found');
    }

    public function getShortMessage()
    {
        return 'EligibilityNotFound';
    }

    public function getDescription()
    {
        return trans('timBR::messages.eligibility.not_found');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_FOUND;
    }
}
