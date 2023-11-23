<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

class RolePermission extends BasePermission
{
    const NAME = "ROLE";

    const CREATE        = PermissionActions::CREATE;
    const EDIT          = PermissionActions::EDIT;
    const DEFINE_PARENT = "DEFINE_PARENT";
}
