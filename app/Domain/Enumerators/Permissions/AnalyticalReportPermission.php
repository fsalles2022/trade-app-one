<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class AnalyticalReportPermission extends BasePermission
{
    const NAME = "ANALYTICAL_REPORT";

    const EXPORT = PermissionActions::EXPORT;
    const VIEW   = PermissionActions::VIEW;
}
