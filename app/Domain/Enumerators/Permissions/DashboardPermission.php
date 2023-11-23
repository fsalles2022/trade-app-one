<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class DashboardPermission extends BasePermission
{
    const NAME = "DASHBOARD";

    const VIEW = PermissionActions::VIEW;
}
