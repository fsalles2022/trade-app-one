<?php

declare(strict_types=1);

namespace SurfPernambucanas\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class PagtelExceptions
{
    public const PLAN_NOT_FOUND    = 'planNotFound';
    public const SURF_UNAVAILABLE  = 'surfUnavailable';
    public const NOT_AUTHENTICATED = 'notAuthenticated';
    public const FAIL_REQUEST      = 'failRequest';

    public static function planNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::PLAN_NOT_FOUND,
            'message'      => trans('surfpernambucanas::exceptions.pagtel.' . self::PLAN_NOT_FOUND),
            'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function surfUnavailable(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::SURF_UNAVAILABLE,
            'message'      => trans('exceptions.third_party_unavailable', ['service' => 'Pernambucanas']),
            'httpCode'     => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }

    public static function notAuthenticated(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_AUTHENTICATED,
            'message'      => trans('surfpernambucanas::exceptions.pagtel.' . self::NOT_AUTHENTICATED),
            'httpCode'     => Response::HTTP_SERVICE_UNAVAILABLE
        ]);
    }

    public static function failRequestByMessage(?string $message = ''): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::FAIL_REQUEST,
            'message'      => trans('surfpernambucanas::exceptions.pagtel.' . self::FAIL_REQUEST, ['message' => $message]),
            'httpCode'     => Response::HTTP_CONFLICT
        ]);
    }
}
