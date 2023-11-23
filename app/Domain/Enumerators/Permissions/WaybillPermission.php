<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class WaybillPermission extends BasePermission
{
    public const NAME = 'WAYBILL';

    public const ALL = PermissionActions::ALL;
}
