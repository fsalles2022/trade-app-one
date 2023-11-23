<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class UserExceptions
{
    const UNAUTHORIZED                          = "userUnauthorized";
    const NOT_PERMISSION_UNDER_ROLE             = "userNotPermissionUnderRole";
    const HAS_NOT_AUTHORIZATION_UNDER_USER      = "userAuthHasNotAuthorizationUnderUser";
    const NOT_BELONGS_TO_POINT_OF_SALE          = "userNotBelongsToPointOfSale";
    const HAS_NOT_AUTHORIZATION_UNDER_HIERARCHY = "userHasNotAuthorizationUnderHierarchy";
    const HAS_NOT_AUTHORIZATION_UNDER_NETWORK   = "userHasNotAuthorizationUnderNetwork";
    const HAS_NOT_AUTHORIZATION_UNDER_QUIZ      = "userHasNotAuthorizationUnderQuiz";
    const NOT_FOUND                             = "userNotFound";
    const NO_NETWORK                            = 'userHasNoNetwork';


    public static function userUnauthorized(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAUTHORIZED,
            'message' => trans('exceptions.user.' . self::UNAUTHORIZED),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function userNotPermissionUnderRole(string $role = ' ')
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_PERMISSION_UNDER_ROLE,
            'message' => trans('exceptions.user.' . self::NOT_PERMISSION_UNDER_ROLE, ['role' => $role]),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function userAuthHasNotAuthorizationUnderUser(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::HAS_NOT_AUTHORIZATION_UNDER_USER,
            'message' => trans('exceptions.user.' . self::HAS_NOT_AUTHORIZATION_UNDER_USER),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function userNotBelongsToPointOfSale(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_BELONGS_TO_POINT_OF_SALE,
            'message' => trans('exceptions.user.' . self::NOT_BELONGS_TO_POINT_OF_SALE),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function userHasNotAuthorizationUnderHierarchy(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::HAS_NOT_AUTHORIZATION_UNDER_HIERARCHY,
            'message' => trans('exceptions.user.' . self::HAS_NOT_AUTHORIZATION_UNDER_HIERARCHY),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function hasNotAuthorizationUnderNetwork(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::HAS_NOT_AUTHORIZATION_UNDER_NETWORK,
            'message' => trans('exceptions.user.' . self::HAS_NOT_AUTHORIZATION_UNDER_NETWORK),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function userNotFound()
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND,
            'message' => trans('exceptions.user.not_found'),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }

    public static function userHasNotAuthorizationUnderQuiz(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::HAS_NOT_AUTHORIZATION_UNDER_QUIZ,
            'message' => trans('exceptions.user.' . self::HAS_NOT_AUTHORIZATION_UNDER_QUIZ),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function userHasNoNetwork(): BuildExceptions
    {
        return new BuildExceptions([
            'shorMessage' => self::NO_NETWORK,
            'message'     => trans('exceptions.user.' . self::NO_NETWORK),
            'httpCode'    => Response::HTTP_FORBIDDEN
        ]);
    }
}
