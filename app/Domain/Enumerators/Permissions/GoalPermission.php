<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class GoalPermission extends BasePermission
{
    const NAME = 'GOAL';

    const IMPORT = PermissionActions::IMPORT;
    const EXPORT = PermissionActions::EXPORT;
}
