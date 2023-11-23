<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class OperationNoExists extends BusinessRuleExceptions
{
    public function getShortMessage()
    {
        return 'OperationNoExists';
    }

    public function getDescription()
    {
        return trans('exceptions.operation_not_found');
    }

    public function getHelp()
    {
        return trans('help.operation_no_exists');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
