<?php


namespace TradeAppOne\Exceptions;

use Illuminate\Http\Response;

final class RemotePaymentException extends BuildExceptions
{
    public const INVALID_SERVICE_PAYMENT_TOKEN = 'invalidServicePaymentToken';
    public const PAYMENT_URL_NOT_CREATED       = 'paymentUrlNotCreated';

    public static function invalidServicePaymentToken(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INVALID_SERVICE_PAYMENT_TOKEN,
            'message' => trans('exceptions.' . self::INVALID_SERVICE_PAYMENT_TOKEN),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function paymentUrlNotCreated(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::PAYMENT_URL_NOT_CREATED,
            'message' => trans('exceptions.' . self::PAYMENT_URL_NOT_CREATED),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
