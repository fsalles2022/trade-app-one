<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;
use Throwable;

class PointOfSaleNotFoundException extends BusinessRuleExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.pos.not_found');
    }

    public function getShortMessage()
    {
        return 'PointOfSaleNotFoundException';
    }

    public function getDescription()
    {
        return trans('exception.pos.not_found');
    }

    public function getHelp()
    {
        return trans('help.pos.not_found');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_FOUND;
    }
}
