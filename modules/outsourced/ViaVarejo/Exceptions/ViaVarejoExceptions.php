<?php


namespace Outsourced\ViaVarejo\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class ViaVarejoExceptions
{
    public const COUPON_NOT_FOUND   = 'couponNotFound';
    public const UNAVAILABLE        = 'viaVarejoUnavailable';
    public const SERVICE_NOT_FOUND  = 'ViaVarejoServiceNotFound';
    public const CUSTOMER_NOT_FOUND = 'ViaVarejoCustomerNotFound';
    public const NOT_ALLOWED        = 'ViaVarejoServiceNotAllowed';

    public static function couponNotFound($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::COUPON_NOT_FOUND,
            'message' => trans('via_varejo::exceptions.' . self::COUPON_NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND,
            'description' => $message
        ]);
    }

    public static function unavailable($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAVAILABLE,
            'message' => trans('via_varejo::exceptions.' . self::UNAVAILABLE),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'description' => $message
        ]);
    }

    public static function serviceNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::SERVICE_NOT_FOUND,
            'message' => trans('via_varejo::exceptions.' . self::SERVICE_NOT_FOUND),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function customerNotFound($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::CUSTOMER_NOT_FOUND,
            'message' => trans('via_varejo::messages.' . self::CUSTOMER_NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND,
            'description' => $message,
        ]);
    }

    public static function serviceNotAllowed($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_ALLOWED,
            'message' => trans('via_varejo::exceptions.' . self::NOT_ALLOWED),
            'httpCode' => Response::HTTP_PRECONDITION_FAILED,
            'description' => $message,
        ]);
    }
}
