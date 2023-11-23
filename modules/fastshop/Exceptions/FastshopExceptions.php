<?php

namespace FastShop\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class FastshopExceptions
{
    public const GENERAL_API_ERROR           = 'GeneralApiError';
    public const SIMULATE_EMPTY_DEVICE_PRICE = 'SimulateReturnEmptyDevicePrice';

    public static function GeneralApiError($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::GENERAL_API_ERROR,
            'message' => trans('fastshop::exceptions.' . self::GENERAL_API_ERROR),
            'httpCode' => Response::HTTP_BAD_REQUEST,
            'description' => $message
        ]);
    }

    public static function SimulateReturnEmptyDevicePrice($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::SIMULATE_EMPTY_DEVICE_PRICE,
            'message' => trans('fastshop::exceptions.' . self::SIMULATE_EMPTY_DEVICE_PRICE),
            'httpCode' => Response::HTTP_BAD_REQUEST,
            'description' => $message
        ]);
    }
}
