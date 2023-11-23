<?php

namespace Uol\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class UolExceptions
{
    const UOL_UNAVAILABLE               = 'uolUnavailable';
    const UOL_ERROR_GENERATING_PASSPORT = 'uolErrorGeneratingPassport';
    const UOL_PRICE_NOT_FOUND           = 'priceNotFound';
    const UOL_ERROR_CANCEL              = 'errorCancelPassport';

    public static function uolUnavailable(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UOL_UNAVAILABLE,
            'message' => trans('uol::exceptions.' . self::UOL_UNAVAILABLE),
            'httpCode' => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }

    public static function uolErrorGeneratingPassport(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UOL_ERROR_GENERATING_PASSPORT,
            'message' => trans('uol::exceptions.' . self::UOL_ERROR_GENERATING_PASSPORT),
            'httpCode' => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }

    public static function priceNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UOL_PRICE_NOT_FOUND,
            'message' => trans('uol::exceptions.' . self::UOL_PRICE_NOT_FOUND),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function errorCancelPassport(?string $message): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UOL_ERROR_CANCEL,
            'message' => trans('uol::exceptions.' . self::UOL_ERROR_CANCEL, ['message' => $message]),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
