<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class ModelInvalidException extends BusinessRuleExceptions
{
    public function getShortMessage()
    {
        return 'ModelInvalidException';
    }

    public function getDescription()
    {
        return $this->message;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
