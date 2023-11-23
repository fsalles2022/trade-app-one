<?php

namespace Discount\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class DiscountExceptions
{
    public const NOT_FOUND                   = 'discountNotFound';
    public const DEVICE_ALREADY_DISCOUNT     = 'deviceAlreadyHasDiscountForPointOfSale';
    public const HAS_NOT_AUTHORIZATION       = 'userHasNotAuthorizationUnderTriangulation';
    public const FAIL_FETCHING_TRIANGULATION = 'failFetchingTriangulation';
    public const ERROR_CHANGING_DATE         = 'errorInChangingDiscountDate';

    public static function notFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND,
            'message' => trans('discount::exceptions.' . self::NOT_FOUND),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function deviceAlreadyHasDiscountForPointOfSale($device): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::DEVICE_ALREADY_DISCOUNT,
            'message' => trans('discount::exceptions.' . self::DEVICE_ALREADY_DISCOUNT, ['device' => $device]),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function userHasNotAuthorizationUnderTriangulation(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::HAS_NOT_AUTHORIZATION,
            'message' => trans('discount::exceptions.' . self::HAS_NOT_AUTHORIZATION),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function failFetchingTriangulation($code): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::FAIL_FETCHING_TRIANGULATION,
            'message' => trans('discount::exceptions.' . self::FAIL_FETCHING_TRIANGULATION),
            'httpCode' => $code
        ]);
    }


    public static function errorInChangingDiscountDate(string $discount, string $exception): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::ERROR_CHANGING_DATE,
            'message' => trans('discount::exceptions.' . self::ERROR_CHANGING_DATE, ['discount' => $discount]) . $exception,
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
