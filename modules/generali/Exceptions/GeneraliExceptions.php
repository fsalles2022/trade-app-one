<?php


namespace Generali\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class GeneraliExceptions
{
    public const INSURANCE_TICKET_NOT_CREATED = 'generaliInsuranceTicketNotCreated';
    public const SERVICE_NOT_ACTIVATED        = 'generaliServiceNotActivated';
    public const INCORRECT_SERVICE_STATUS     = 'incorrectServiceStatus';
    public const SERVICE_NOT_CANCELLED        = 'serviceNotCancelled';
    public const UNAVAILABLE                  = 'generaliUnavailable';
    public const PRODUCT_NOT_FOUND            = 'productNotFound';

    public static function insuranceTicketNotCreated(\Exception $exception): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INSURANCE_TICKET_NOT_CREATED,
            'message'      => trans('generali::exceptions.' . self::INSURANCE_TICKET_NOT_CREATED),
            'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY,
            'description'  => $exception->getMessage()
        ]);
    }

    public static function serviceNotActivated(): BuildExceptions
    {
        return new BuildExceptions([
           'shortMessage' => self::SERVICE_NOT_ACTIVATED,
           'message'      => trans('generali::exceptions.' . self::SERVICE_NOT_ACTIVATED),
           'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function incorrectServiceStatus(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INCORRECT_SERVICE_STATUS,
            'message'      => trans('generali::exceptions.' . self::INCORRECT_SERVICE_STATUS),
            'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function serviceNotCancelled(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage'      => self::SERVICE_NOT_CANCELLED,
            'message'           => trans('generali::exceptions.' . self::SERVICE_NOT_CANCELLED),
            'httpCode'          => Response::HTTP_UNPROCESSABLE_ENTITY,
        ]);
    }

    public static function unavailable($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAVAILABLE,
            'message' => trans('generali::exceptions.' . self::UNAVAILABLE),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'description' => $message
        ]);
    }

    public static function productNotFound(array $array): BuildExceptions
    {
        $value = data_get($array, 'devicePrice', '');

        return new BuildExceptions([
            'shortMessage' => self::PRODUCT_NOT_FOUND,
            'message' => trans('generali::exceptions.' . self::PRODUCT_NOT_FOUND, ['value' => $value]),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
        ]);
    }
}
