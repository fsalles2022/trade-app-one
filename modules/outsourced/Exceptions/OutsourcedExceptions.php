<?php

namespace Outsourced\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class OutsourcedExceptions
{
    const SERVICE_NOT_FOUND = 'OutsourcedServiceNotFound';

    public static function serviceNotFound()
    {
        return new BuildExceptions([
            'shortMessage' => self::SERVICE_NOT_FOUND,
            'message' => trans('outsourced::exceptions.' . self::SERVICE_NOT_FOUND),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
