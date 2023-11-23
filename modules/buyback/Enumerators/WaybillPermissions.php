<?php

namespace Buyback\Enumerators;

use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\BasePermission;

final class WaybillPermissions extends BasePermission
{
    public const NAME = 'WAYBILL';

    public const CREATE = PermissionActions::CREATE;
    public const VIEW   = PermissionActions::VIEW;
}
