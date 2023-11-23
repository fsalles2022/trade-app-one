<?php

declare(strict_types=1);

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Exceptions\BuildExceptions;

class SivAutomaticRegistrationExceptions
{
    public const USER_ALREADY_EXISTS                 = 'userAlreadyExists';
    public const INVALID_SIV_OPERATION               = 'invalidSivOperation';
    public const NOT_HAVE_ROLES_FROM_USER            = 'notHaveRolesFromUser';
    public const USER_NOT_BE_CREATED                 = 'userNotBeCreated';
    public const POINT_OF_SALE_NOT_EXISTS_IN_NETWORK = 'pointOfSaleNotExistsInNetwork';
    public const USER_AUTH_ALTERNATE_ALREADY_EXISTS  = 'userAuthAlternateAlreadyExists';
    public const GENERIC_SIV_ERROR                   = 'genericSivError';

    public static function userAlreadyExists(?string $message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::USER_ALREADY_EXISTS,
            'message' => trans('siv::exceptions.AutomaticRegistration.' . self::USER_ALREADY_EXISTS),
            'httpCode' => Response::HTTP_PRECONDITION_FAILED,
            'description' => $message
        ]);
    }

    public static function invalidSivOperation(?string $message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INVALID_SIV_OPERATION,
            'message' => trans('siv::exceptions.AutomaticRegistration.' . self::INVALID_SIV_OPERATION),
            'httpCode' => Response::HTTP_PRECONDITION_FAILED,
            'description' => $message
        ]);
    }

    public static function notHaveRolesFromUser(?string $message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_HAVE_ROLES_FROM_USER,
            'message' => trans('siv::exceptions.AutomaticRegistration.' . self::NOT_HAVE_ROLES_FROM_USER),
            'httpCode' => Response::HTTP_PRECONDITION_FAILED,
            'description' => $message
        ]);
    }

    public static function userNotBeCreated(?string $message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::USER_NOT_BE_CREATED,
            'message' => trans('siv::exceptions.AutomaticRegistration.' . self::USER_NOT_BE_CREATED),
            'httpCode' => Response::HTTP_BAD_REQUEST,
            'description' => $message
        ]);
    }

    public static function pointOfSaleNotExistsInNetwork(?string $message = null): BuildExceptions
    {
        return new BuildExceptions([
           'shortMessage' => self::POINT_OF_SALE_NOT_EXISTS_IN_NETWORK,
           'message'      => trans('nada'),
           'httpCode'     =>  Response::HTTP_PRECONDITION_FAILED,
            'description' => $message
        ]);
    }

    public static function userAuthAlternateAlreadyExists(?string $message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::USER_AUTH_ALTERNATE_ALREADY_EXISTS,
            'message'      => trans('checkpoint'),
            'httpCode'     => Response::HTTP_CONFLICT,
            'description'  => $message
        ]);
    }

    public static function genericSivError(Responseable $response): BuildExceptions
    {
        $message = $response->get('message');

        return new SivAutomaticRegistrationGenericException($message, $response);
    }
}
