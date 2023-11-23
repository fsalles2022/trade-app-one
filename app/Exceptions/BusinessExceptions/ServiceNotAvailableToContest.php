<?php

declare(strict_types=1);

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class ServiceNotAvailableToContest extends BusinessRuleExceptions
{

    public function getShortMessage()
    {
        return 'ServiceNotAvailableToContest';
    }

    public function getDescription()
    {
        return trans('exceptions.service_not_available_for_contest');
    }

    public function getHelp()
    {
        return trans('help.service_not_available_for_contest');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
