<?php

namespace Buyback\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BusinessExceptions\BusinessRuleExceptions;

class EvaluationNotFoundException extends BusinessRuleExceptions
{
    public function __construct(string $description = '')
    {
        $this->description = $description;
        $this->message     = trans('buyback::exceptions.evaluation_not_found_exception.message');
    }

    public function getShortMessage()
    {
        return 'EvaluationNotFoundException';
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
