<?php

namespace Buyback\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class TradeInExceptions
{
    const IMEI_ALREADY_EXISTS              = 'imeiAlreadyExists';
    const VOUCHER_ALREADY_CANCELED         = 'voucherAlreadyCanceled';
    const VOUCHER_NOT_BELONGS_TO_OPERATION = 'voucherNotBelongsToOperation';
    const DEVICE_NOT_BELONG_TO_NETWORK     = 'deviceNotBelongToNetwork';

    public static function voucherAlreadyCanceled(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::VOUCHER_ALREADY_CANCELED,
            'message' => trans('buyback::exceptions.'. self::VOUCHER_ALREADY_CANCELED),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function imeiAlreadyExists() :BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::IMEI_ALREADY_EXISTS,
            'message' => trans('exceptions.' . self::IMEI_ALREADY_EXISTS),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function deviceNotBelongToNetwork() :BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::DEVICE_NOT_BELONG_TO_NETWORK,
            'message' => trans('buyback::exceptions.' . self::DEVICE_NOT_BELONG_TO_NETWORK),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function voucherNotBelongsToOperation($operation) :BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::VOUCHER_NOT_BELONGS_TO_OPERATION,
            'message' => trans('buyback::exceptions.' . self::VOUCHER_NOT_BELONGS_TO_OPERATION, [
                'operations' => $operation
            ]),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
