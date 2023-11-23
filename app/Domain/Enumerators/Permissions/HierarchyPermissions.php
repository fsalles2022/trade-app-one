<?php


namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class HierarchyPermissions extends BasePermission
{
    public const NAME = 'HIERARCHY';

    public const CREATE = PermissionActions::CREATE;
}
