<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class RecoveryPermission extends BasePermission
{
    const NAME = "RECOVERY";

    const APPROVE = PermissionActions::APPROVE;
    const VIEW    = PermissionActions::VIEW;
    const REJECT  = PermissionActions::REJECT;
}
