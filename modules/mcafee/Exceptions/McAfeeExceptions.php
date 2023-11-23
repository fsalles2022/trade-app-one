<?php

namespace McAfee\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class McAfeeExceptions
{
    const MCAFEE_UNAVAILABLE                               = 'mcAfeeUnavailable';
    const MCAFEE_ERROR_ACTIVATING_THE_SALE                 = 'mcAfeeErrorActivatingTheSale';
    const MCAFEE_ERROR_CANCELING_SUBSCRIPTION              = 'mcAfeeErrorCancelingSubscription';
    const MCAFEE_ERROR_DISCONNECTING_DEVICES               = 'mcAfeeErrorDisconnectingDevices';
    const MCAFEE_SERVICE_IS_NOT_APPROVED_TO_CANCEL         = 'mcAfeeServiceIsNotApprovedToCancel';
    const MCAFEE_DATE_IS_GREATER_THAN_SEVEN_DAYS_TO_CANCEL = 'mcAfeeDateIsGreaterThanSevenDaysToCancel';

    public static function mcAfeeUnavailable(?string $mcAfeeMessage = ''): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::MCAFEE_UNAVAILABLE,
            'message' => trans('exceptions.third_party_unavailable', ['service' => 'McAfee']),
            'httpCode' => Response::HTTP_MISDIRECTED_REQUEST,
            'transportedMessage' => $mcAfeeMessage
        ]);
    }

    public static function mcAfeeErrorActivatingTheSale(?string $code)
    {
        return new BuildExceptions([
            'shortMessage' => self::MCAFEE_ERROR_ACTIVATING_THE_SALE,
            'message' => trans(
                'mcAfee::exceptions.'. McAfeeExceptions::MCAFEE_ERROR_ACTIVATING_THE_SALE,
                ['code' => $code]
            ),
            'httpCode' => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }

    public static function mcAfeeErrorCancelingSubscription(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::MCAFEE_ERROR_CANCELING_SUBSCRIPTION,
            'message' => trans('mcAfee::exceptions.'. McAfeeExceptions::MCAFEE_ERROR_CANCELING_SUBSCRIPTION),
            'httpCode' => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }

    public static function mcAfeeErrorDisconnectingDevices(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::MCAFEE_ERROR_DISCONNECTING_DEVICES,
            'message' => trans('mcAfee::exceptions.'. McAfeeExceptions::MCAFEE_ERROR_DISCONNECTING_DEVICES),
            'httpCode' => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }

    public static function mcAfeeServiceIsNotApprovedToCancel(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::MCAFEE_SERVICE_IS_NOT_APPROVED_TO_CANCEL,
            'message' => trans('mcAfee::exceptions.'.
                McAfeeExceptions::MCAFEE_SERVICE_IS_NOT_APPROVED_TO_CANCEL),
            'httpCode' => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }

    public static function mcAfeeDateIsGreaterThanSevenDaysToCancel(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::MCAFEE_DATE_IS_GREATER_THAN_SEVEN_DAYS_TO_CANCEL,
            'message' => trans('mcAfee::exceptions.'.
                McAfeeExceptions::MCAFEE_DATE_IS_GREATER_THAN_SEVEN_DAYS_TO_CANCEL),
            'httpCode' => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }
}
