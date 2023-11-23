<?php


namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

class RefusedSaleReportPermission extends BasePermission
{
    public const NAME = 'DENIED_MAILING';

    public const VIEW   = PermissionActions::VIEW;
    public const EXPORT = PermissionActions::EXPORT;
}
