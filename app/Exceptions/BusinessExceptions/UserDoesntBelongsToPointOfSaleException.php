<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class UserDoesntBelongsToPointOfSaleException extends BusinessRuleExceptions
{
    public function getShortMessage()
    {
        return 'UserDoesntBelongsToPointOfSaleException';
    }

    public function getDescription()
    {
        return trans('exceptions.user_doesnt_belongs_to_point_of_sale');
    }

    public function getHelp()
    {
        return trans('help.user_doesnt_belongs_to_point_of_sale');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
