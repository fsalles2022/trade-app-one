<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class RoleNotFoundException extends BusinessRuleExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.role.not_found');
    }

    public function getShortMessage()
    {
        return 'RoleNotFoundException';
    }

    public function getDescription()
    {
        return trans('exception.role.not_found');
    }

    public function getHelp()
    {
        return trans('help.role.not_found');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_FOUND;
    }
}
