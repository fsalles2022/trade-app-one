<?php

namespace Reports\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Exceptions\CustomRuleExceptions;

class FailedReportBuildException extends CustomRuleExceptions
{

    public function __construct(string $message = "")
    {
        $this->transportedMessage = $message;
        $this->message            = trans('exceptions.failed_report_build');
    }

    public function getShortMessage()
    {
        return 'FailedReportBuild';
    }

    public function getDescription()
    {
        return trans('exceptions.failed_report_build');
    }

    public function getTransportedMessage()
    {
        return $this->transportedMessage;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getHelp()
    {
        return trans('exceptions.failed_report_build');
    }
}
