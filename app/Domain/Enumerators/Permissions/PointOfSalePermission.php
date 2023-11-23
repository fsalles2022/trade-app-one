<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class PointOfSalePermission extends BasePermission
{
    const NAME = "POINT_OF_SALE";

    const VIEW   = PermissionActions::VIEW;
    const CREATE = PermissionActions::CREATE;
    const EDIT   = PermissionActions::EDIT;
    const EXPORT = PermissionActions::EXPORT;
}
