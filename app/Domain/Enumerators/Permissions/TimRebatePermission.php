<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class TimRebatePermission extends BasePermission
{
    const NAME = "TIM_REBATE";

    const CREATE = PermissionActions::CREATE;
    const USE = PermissionActions::USE;
}
