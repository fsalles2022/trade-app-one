<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class ManagementReportPermission extends BasePermission
{
    const NAME = "MANAGEMENT_REPORT";

    const VIEW = PermissionActions::VIEW;
}
