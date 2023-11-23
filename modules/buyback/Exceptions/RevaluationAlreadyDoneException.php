<?php

namespace Buyback\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BusinessExceptions\BusinessRuleExceptions;

class RevaluationAlreadyDoneException extends BusinessRuleExceptions
{
    public function __construct(string $description = '')
    {
        $this->description = $description;
        $this->message     = trans('buyback::exceptions.revaluation_already_done_exception.message');
    }

    public function getShortMessage()
    {
        return 'RevaluationAlreadyDoneException';
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_ACCEPTABLE;
    }
}
