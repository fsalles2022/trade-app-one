<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class UserPermission extends BasePermission
{
    public const NAME = "USER";

    public const EXPORT               = PermissionActions::EXPORT;
    public const PERSONIFY            = PermissionActions::PERSONIFY;
    public const VIEW                 = PermissionActions::VIEW;
    public const CREATE               = PermissionActions::CREATE;
    public const DELETE               = PermissionActions::DELETE;
    public const PASSWORD_MASS_UPDATE = 'PASSWORD_MASS_UPDATE';
}
