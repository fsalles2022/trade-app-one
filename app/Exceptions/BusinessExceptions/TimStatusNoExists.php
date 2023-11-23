<?php

declare(strict_types=1);

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class TimStatusNoExists extends BusinessRuleExceptions
{

    public function getShortMessage()
    {
        return 'TimStatusNoExists';
    }

    public function getDescription()
    {
        return trans('exceptions.tim_status_no_exists');
    }

    public function getHelp()
    {
        return trans('help.tim_status_no_exists');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
