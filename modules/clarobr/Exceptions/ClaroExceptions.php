<?php

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class ClaroExceptions
{
    public const REBATE_WITH_INVALID_STRUCTURE         = 'rebateWithInvalidStructure';
    public const CONTEST_INVALID_RESPONSE              = 'contestInvalidResponse';
    public const CONTEST_UNAVAILABLE                   = 'contestUnavailable';
    public const UPDATE_ERROR                          = 'sivUpdateError';
    public const AUTHENTICATE_WITHOUT_POINTOFSALE_CODE = 'authenticateWithoutPointOfSaleCode';
    public const BR_SCAN_INVALID_RESPONSE              = 'brScanInvalidResponse';
    public const PAYMENT_URL_NOT_FOUND                 = 'paymentUrlNotFound';


    public static function REBATE_WITH_INVALID_STRUCTURE(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::REBATE_WITH_INVALID_STRUCTURE,
            'message' => trans('siv::exceptions.' . self::REBATE_WITH_INVALID_STRUCTURE),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function CONTEST_INVALID_RESPONSE(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::CONTEST_INVALID_RESPONSE,
            'message' => trans('siv::exceptions.' . self::CONTEST_INVALID_RESPONSE),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function CONTEST_UNAVAILABLE($message): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::CONTEST_UNAVAILABLE,
            'message' => $message,
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function updateError($description = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UPDATE_ERROR,
            'message'      => trans('siv::exceptions.' . self::UPDATE_ERROR),
            'description'  => $description,
            'httpCode'     => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }

    public static function authenticateWithoutPointOfSaleCode($description = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::AUTHENTICATE_WITHOUT_POINTOFSALE_CODE,
            'message'      => trans('siv::exceptions.' . self::AUTHENTICATE_WITHOUT_POINTOFSALE_CODE),
            'description'  => $description,
            'httpCode'     => Response::HTTP_PRECONDITION_FAILED
        ]);
    }

    public static function brScanInvalidResponse($description = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::BR_SCAN_INVALID_RESPONSE,
            'message'      => trans('siv::exceptions.' . self::BR_SCAN_INVALID_RESPONSE),
            'description'  => $description,
            'httpCode'     => Response::HTTP_PRECONDITION_FAILED
        ]);
    }

    public static function paymentUrlNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::PAYMENT_URL_NOT_FOUND,
            'message'      => trans('siv::exceptions.' . self::PAYMENT_URL_NOT_FOUND),
            'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
