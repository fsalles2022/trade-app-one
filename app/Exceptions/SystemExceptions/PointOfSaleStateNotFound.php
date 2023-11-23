<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\CustomRuleExceptions;

class PointOfSaleStateNotFound extends CustomRuleExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.point_of_sale_state_not_found.message');
    }

    public function getShortMessage()
    {
        return 'PointOfSaleStateNotFound';
    }

    public function getDescription()
    {
        return trans('exceptions.point_of_sale_state_not_found.message');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
