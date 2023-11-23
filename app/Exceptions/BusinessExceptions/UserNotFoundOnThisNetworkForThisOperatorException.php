<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class UserNotFoundOnThisNetworkForThisOperatorException extends BusinessRuleExceptions
{

    public function getShortMessage()
    {
        return 'UserNotFoundOnThisNetworkForThisOperatorException';
    }

    public function getDescription()
    {
        return trans('exceptions.export.user_not_found_on_this_network_for_this_operator');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_FOUND;
    }
}
