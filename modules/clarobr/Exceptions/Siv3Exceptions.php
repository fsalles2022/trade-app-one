<?php

declare(strict_types=1);

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class Siv3Exceptions
{
    public const INVALID_CREDENTIALS    = 'InvalidCredentials';
    public const ADDRESS_NOT_FOUND      = 'addressNotFound';
    public const UNAVAILABLE_SERVICE    = 'unavailableService';
    public const INVALID_CODE           = 'invalidCode';
    public const UNAUTHORIZED_OPERATION = 'unauthorizedOperation';

    public static function invalidCredentials(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INVALID_CREDENTIALS,
            'message'      => trans('siv::exceptions.' . self::INVALID_CREDENTIALS),
            'httpCode'     => Response::HTTP_UNAUTHORIZED
        ]);
    }

    public static function addressNotFound(array $content): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::ADDRESS_NOT_FOUND,
            'message'      => $content['message'] ?? trans('siv::exceptions.' . self::ADDRESS_NOT_FOUND),
            'httpCode'     => Response::HTTP_UNAUTHORIZED
        ]);
    }

    public static function unavailableService(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAVAILABLE_SERVICE,
            'message' => trans('siv::exceptions.' . self::UNAVAILABLE_SERVICE),
            'httpCode' => Response::HTTP_SERVICE_UNAVAILABLE
        ]);
    }

    public static function invalidCode(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INVALID_CODE,
            'message' => trans('siv::exceptions.' . self::INVALID_CODE),
            'httpCode' => Response::HTTP_BAD_REQUEST
        ]);
    }

    public static function unauthorizedOperation(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAUTHORIZED_OPERATION,
            'message' => trans('siv::exceptions.' . self::UNAUTHORIZED_OPERATION),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
