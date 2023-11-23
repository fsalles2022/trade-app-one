<?php

namespace NextelBR\Exceptions;

use Illuminate\Http\Response;
use NextelBR\Enumerators\NextelBRAreaCodes;
use TradeAppOne\Exceptions\BusinessExceptions\BusinessRuleExceptions;

class AreaCodeNotAcceptable extends BusinessRuleExceptions
{
    public function __construct()
    {
        $this->message = trans(
            'nextelBR::exceptions.AreaCodeNotAcceptable.message',
            ['areaCodes' => NextelBRAreaCodes::areaCodesString()]
        );
    }

    public function getShortMessage()
    {
        return 'AreaCodeNotAcceptable';
    }

    public function getDescription()
    {
        return trans(
            'nextelBR::exceptions.AreaCodeNotAcceptable.message',
            ['areaCodes' => NextelBRAreaCodes::areaCodesString()]
        );
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_ACCEPTABLE;
    }
}
