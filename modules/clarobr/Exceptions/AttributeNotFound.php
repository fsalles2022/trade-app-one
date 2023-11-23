<?php

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\CustomRuleExceptions;

class AttributeNotFound extends CustomRuleExceptions
{
    public function __construct(string $message = "")
    {
        $this->message = trans('siv::exceptions.AttributeNotFound.message', ['attribute' => $message]);
    }

    public function getShortMessage()
    {
        return 'AttributeNotFound';
    }

    public function getDescription()
    {
        trans('siv::exceptions.AttributeNotFound.description');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_FOUND;
    }
}
