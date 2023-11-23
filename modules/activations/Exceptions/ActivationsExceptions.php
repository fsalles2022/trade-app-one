<?php


namespace GA\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class ActivationsExceptions
{
    public const UNAVAILABLE = 'gatewayActivationsUnavailable';

    public static function unavailable($message = ''): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAVAILABLE,
            'message'      => trans('activations::exceptions.' . self::UNAVAILABLE),
            'transportedMessage' => $message,
            'httpCode'     => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }
}
