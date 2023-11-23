<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class SectorNoExists extends BusinessRuleExceptions
{

    public function getShortMessage()
    {
        return 'SectorOfOperatorNoExists';
    }

    public function getDescription()
    {
        return trans('exceptions.sector_no_exists');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
