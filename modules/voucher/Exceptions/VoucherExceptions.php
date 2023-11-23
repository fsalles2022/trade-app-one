<?php

namespace Voucher\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class VoucherExceptions
{
    public const NOT_FOUND                               = 'NotFound';
    public const VOUCHER_BURNED                          = 'VoucherBurned';
    public const VOUCHER_INCORRECT_STATUS                = 'VoucherIncorrectStatus';
    public const VOUCHER_NOT_BELONGS_TO_NETWORK          = 'VoucherNotBelongsToNetwork';
    public const VOUCHER_EXPIRED                         = 'VoucherExpired';
    public const VOUCHER_TELECOMMUNICATION_DIFERENT_IMEI = 'VoucherTelecommunicationDifferentImei';
    public const VOUCHER_NOT_BURNED_WHEN_TRYING_CANCEL   = 'VoucherNotBurnedWhenTryingCancel';
    public const VOUCHER_INCORRECT_VALUES_FROM_METADATA  = 'IncorrectValuesFromVoucherMetadata';
    public const ONLY_OPERATOR_SALE_IS_ALLOWED           = 'OnlyOperatorSaleIsAllowed';
    public const NO_TRIANGULATION_FOR_IMEI               = 'NoTriangulationForImei';
    public const NO_OTHER_TRIANGULATION_IN_OPERATOR      = 'NoOtherTriangulationInOperator';

    public static function NotFound($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND,
            'message' => trans('voucher::exceptions.' . self::NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND,
            'description' => $message
        ]);
    }

    public static function VoucherBurned($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::VOUCHER_BURNED,
            'message' => trans('voucher::exceptions.' . self::VOUCHER_BURNED),
            'httpCode' => Response::HTTP_CONFLICT,
            'description' => $message
        ]);
    }

    public static function VoucherIncorrectStatus($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::VOUCHER_INCORRECT_STATUS,
            'message' => trans('voucher::exceptions.' . self::VOUCHER_INCORRECT_STATUS),
            'httpCode' => Response::HTTP_CONFLICT,
            'description' => $message
        ]);
    }

    public static function VoucherNotBelongsToNetwork($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::VOUCHER_NOT_BELONGS_TO_NETWORK,
            'message' => trans('voucher::exceptions.' . self::VOUCHER_NOT_BELONGS_TO_NETWORK),
            'httpCode' => Response::HTTP_CONFLICT,
            'description' => $message
        ]);
    }

    public static function VoucherExpired($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::VOUCHER_EXPIRED,
            'message' => trans('voucher::exceptions.' . self::VOUCHER_EXPIRED),
            'httpCode' => Response::HTTP_CONFLICT,
            'description' => $message
        ]);
    }

    public static function VoucherTelecommunicationDifferentImei($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::VOUCHER_TELECOMMUNICATION_DIFERENT_IMEI,
            'message' => trans('voucher::exceptions.' . self::VOUCHER_TELECOMMUNICATION_DIFERENT_IMEI),
            'httpCode' => Response::HTTP_CONFLICT,
            'description' => $message
        ]);
    }

    public static function VoucherNotBurnedWhenTryingCancel($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::VOUCHER_NOT_BURNED_WHEN_TRYING_CANCEL,
            'message' => trans('voucher::exceptions.' . self::VOUCHER_NOT_BURNED_WHEN_TRYING_CANCEL),
            'httpCode' => Response::HTTP_PRECONDITION_FAILED,
            'description' => $message
        ]);
    }

    public static function IncorrectValuesFromVoucherMetadata($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::VOUCHER_INCORRECT_VALUES_FROM_METADATA,
            'message' => trans('voucher::exceptions.' . self::VOUCHER_INCORRECT_VALUES_FROM_METADATA),
            'httpCode' => Response::HTTP_PRECONDITION_FAILED,
            'description' => $message
        ]);
    }

    public static function OnlyOperatorSaleIsAllowed($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::ONLY_OPERATOR_SALE_IS_ALLOWED,
            'message' => trans('voucher::exceptions.' . self::ONLY_OPERATOR_SALE_IS_ALLOWED),
            'httpCode' => Response::HTTP_PRECONDITION_FAILED,
            'description' => $message
        ]);
    }

    public static function NoTriangulationForImei($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NO_TRIANGULATION_FOR_IMEI,
            'message' => trans('voucher::exceptions.' . self::NO_TRIANGULATION_FOR_IMEI),
            'httpCode' => Response::HTTP_NOT_FOUND,
            'description' => $message
        ]);
    }

    public static function NoOtherTriangulationInOperator($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NO_OTHER_TRIANGULATION_IN_OPERATOR,
            'message' => trans('voucher::exceptions.' . self::NO_OTHER_TRIANGULATION_IN_OPERATOR),
            'httpCode' => Response::HTTP_CONFLICT,
            'description' => $message
        ]);
    }
}
