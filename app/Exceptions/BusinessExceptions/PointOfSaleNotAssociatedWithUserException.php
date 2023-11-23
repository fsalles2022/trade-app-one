<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class PointOfSaleNotAssociatedWithUserException extends BusinessRuleExceptions
{
    public function __construct()
    {
        $this->message = trans('exceptions.pos.not_associated_with_user');
    }
    
    public function getShortMessage()
    {
        return 'PointOfSaleNotAssociatedWithUserException';
    }

    public function getDescription()
    {
        return trans('exceptions.pos.not_associated_with_user');
    }

    public function getHelp()
    {
        return trans('help.pos.not_associated_with_user');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
