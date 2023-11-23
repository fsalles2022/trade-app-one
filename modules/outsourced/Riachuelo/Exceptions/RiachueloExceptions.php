<?php

namespace Outsourced\Riachuelo\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class RiachueloExceptions
{
    const UNAVAILABLE = 'riachueloUnavailable';

    public static function unavailable($message = null)
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAVAILABLE,
            'message' => trans('riachuelo::exceptions.' . self::UNAVAILABLE),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'description' => $message
        ]);
    }
}
