<?php


namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class ServiceOptionsExceptions
{
    public const SERVICE_OPTIONS_OPERATIONS_NOT_AVAILABLE = "serviceOptionsOperationsNotAvailable";
    public const ACTION_SERVICE_OPTIONS_NOT_FOUND         = "actionServiceOptionsNotFound";

    public static function serviceOptionsOperationsNotAvailable(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::SERVICE_OPTIONS_OPERATIONS_NOT_AVAILABLE,
            'message'      => trans('exceptions.serviceOptions.' . self::SERVICE_OPTIONS_OPERATIONS_NOT_AVAILABLE),
            'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function actionServiceOptionsNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::ACTION_SERVICE_OPTIONS_NOT_FOUND,
            'message'      => trans('exceptions.serviceOptions.' . self::ACTION_SERVICE_OPTIONS_NOT_FOUND),
            'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
