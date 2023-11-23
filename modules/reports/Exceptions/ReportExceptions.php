<?php

namespace Reports\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class ReportExceptions
{
    public const FAILED_REPORT_BUILD      = 'failedReportBuild';
    public const REPORT_FILTERS_REQUESTED = 'reportFiltersRequestedExceptions';

    public static function failedReportBuild(string $message = '')
    {
        return new BuildExceptions([
            'shortMessage'       => self::FAILED_REPORT_BUILD,
            'message'            => trans('exceptions.' . self::FAILED_REPORT_BUILD),
            'httpCode'           => Response::HTTP_BAD_REQUEST,
            'transportedMessage' => $message
        ]);
    }

    public static function filterRequestedReportBuild(string $message = '')
    {
        return new BuildExceptions([
            'shortMessage'       => self::REPORT_FILTERS_REQUESTED,
            'message'            => trans('exceptions.' . self::REPORT_FILTERS_REQUESTED),
            'httpCode'           => Response::HTTP_CONFLICT,
            'transportedMessage' => $message
        ]);
    }
}
