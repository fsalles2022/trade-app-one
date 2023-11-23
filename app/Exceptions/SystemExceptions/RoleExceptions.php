<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class RoleExceptions
{
    const ROLE_NOT_FOUND = 'roleNotFound';

    public static function ROLE_ALREADY_EXISTS_REGISTERED()
    {
        throw new BuildExceptions([
             'shortMessage' => 'RoleAlreadyExistsRegistered',
             'message' => trans('exceptions.role.role_exists'),
             'httpCode' => Response::HTTP_CONFLICT
             ]);
    }

    public static function USER_CAN_NOT_ASSIGN_PERMISSION_TO_ROLE(string $permission)
    {
        throw new BuildExceptions([
            'shortMessage' => 'UserCanNotAssignPermissionToRole',
            'message' => trans('exceptions.role.assign_permission', ['permission' => $permission]),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function USER_HAS_NOT_AUTHORITY_UNDER_ROLE()
    {
        throw new BuildExceptions([
            'shortMessage' => 'userHasNotAuthorityUnderRole',
            'message' => trans('exceptions.role.has_not_authority_under_role'),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function USER_NOT_CAN_ADD_ROLE_IN_THIS_NETWORK()
    {
        throw new BuildExceptions([
            'shortMessage' => 'UserNotCanAddRoleInThisNetwork',
            'message' => trans('exceptions.role.not_can_add_in_network'),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function USER_AUTH_CAN_NOT_ADD_PARENT()
    {
        throw new BuildExceptions([
            'shortMessage' => 'UserAuthCanNotAddThisParent',
            'message' => trans('exceptions.role.can_not_add_parent'),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }

    public static function PARENT_NOT_BELONGS_TO_NETWORK()
    {
        throw new BuildExceptions([
            'shortMessage' => 'ParentNotBelongsToNetwork',
            'message' => trans('exceptions.role.not_belongs_to_network'),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function roleNotFound()
    {
        return new BuildExceptions([
            'shortMessage' => 'roleNotFound',
            'message' => trans('exceptions.role.not_found'),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }
}
