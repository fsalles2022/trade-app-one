<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class NetworkNotAssociatedWithUserException extends BusinessRuleExceptions
{
    public function __construct(string $description = '')
    {
        $this->description = $description;
        $this->message     = trans('exceptions.network.not_associated_with_user');
    }

    public function getShortMessage()
    {
        return 'NetworkNotAssociatedWithUserException';
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
