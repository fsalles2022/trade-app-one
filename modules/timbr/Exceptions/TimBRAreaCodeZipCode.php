<?php

namespace TimBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BusinessExceptions\BusinessRuleExceptions;

class TimBRAreaCodeZipCode extends BusinessRuleExceptions
{
    public function __construct()
    {
        $this->code    = 'TimBRAreaCodeZipCodeValidation';
        $this->message = trans('timBR::exceptions.eligibility.area_code_validation');
    }

    public function getShortMessage()
    {
        return 'TimBRAreaCodeZipCodeValidation';
    }

    public function getDescription()
    {
        return '';
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
