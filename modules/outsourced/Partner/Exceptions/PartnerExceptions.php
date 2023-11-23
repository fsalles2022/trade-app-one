<?php

namespace Outsourced\Partner\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class PartnerExceptions
{

    public const UNAVAILABLE                = 'verifyUrlPartnerUnavailable';
    public const NOT_FOUND                  = 'notFound';
    public const NOT_FOUND_DEFAULT_REDIRECT = 'notFoundDefaultRedirectUrl';
    public const USER_NOT_BELONGS           = 'userNotBelongsPartner';
    public const NOT_IMPLEMENTED            = 'partnerNotImplemented';
    public const REQUEST_ERROR              = 'errorWhenGetPartnerIdentification';
    public const INVALID_TOKEN              = 'tokenInvalidOrExpired';
    public const INVALID_CREDENTIAL_URL     = 'invalidCredentialUrl';

    public static function unavailable($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAVAILABLE,
            'message' => trans('partner::exceptions.' . self::UNAVAILABLE),
            'httpCode' => Response::HTTP_BAD_REQUEST,
            'description' => $message
        ]);
    }

    public static function notFound($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND,
            'message' => trans('partner::exceptions.' . self::NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND,
            'description' => $message
        ]);
    }

    public static function partnerNotImplemented($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_IMPLEMENTED,
            'message' => trans('partner::exceptions.' . self::NOT_IMPLEMENTED),
            'httpCode' => Response::HTTP_BAD_REQUEST,
            'description' => $message
        ]);
    }

    public static function userNotBelongsPartner($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::USER_NOT_BELONGS,
            'message' => trans('partner::exceptions.' . self::USER_NOT_BELONGS),
            'httpCode' => Response::HTTP_BAD_REQUEST,
            'description' => $message
        ]);
    }

    public static function errorWhenGetPartnerIdentification($message = null, $httpCode = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::REQUEST_ERROR,
            'message' => trans('partner::exceptions.' . self::REQUEST_ERROR),
            'httpCode' => $httpCode ?? Response::HTTP_BAD_REQUEST,
            'description' => $message
        ]);
    }

    public static function tokenInvalidOrExpired($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INVALID_TOKEN,
            'message' => trans('partner::exceptions.' . self::INVALID_TOKEN),
            'httpCode' => Response::HTTP_BAD_REQUEST,
            'description' => $message
        ]);
    }

    public static function invalidCredentialUrl($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INVALID_CREDENTIAL_URL,
            'message' => trans('partner::exceptions.' . self::INVALID_CREDENTIAL_URL),
            'httpCode' => Response::HTTP_BAD_REQUEST,
            'description' => $message
        ]);
    }

    public static function notFoundDefaultRedirectUrl($message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND_DEFAULT_REDIRECT,
            'message' => trans('partner::exceptions.' . self::NOT_FOUND_DEFAULT_REDIRECT),
            'httpCode' => Response::HTTP_NOT_FOUND,
            'description' => $message
        ]);
    }
}
