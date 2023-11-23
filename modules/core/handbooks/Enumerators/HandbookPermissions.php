<?php

namespace Core\HandBooks\Enumerators;

use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\BasePermission;

final class HandbookPermissions extends BasePermission
{
    const NAME = 'HANDBOOK';

    const CREATE = PermissionActions::CREATE;
    const EDIT   = PermissionActions::EDIT;
    const DELETE = PermissionActions::DELETE;
}
