<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class DashboardLadsPermission extends BasePermission
{
    public const NAME = "DASHBOARD_LADS";

    public const VIEW     = PermissionActions::VIEW;
    public const VIEW_ALL = "VIEW_ALL";
}
