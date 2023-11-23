<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class OperatorNoExists extends BusinessRuleExceptions
{
    public function getShortMessage()
    {
        return 'OperatorNotFound';
    }

    public function getDescription()
    {
        return trans('exceptions.operator_not_found');
    }

    public function getHelp()
    {
        return trans('help.operator_not_found');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
