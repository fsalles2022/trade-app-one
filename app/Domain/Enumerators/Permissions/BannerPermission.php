<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class BannerPermission extends BasePermission
{
    const NAME = "BANNER";

    const CREATE = PermissionActions::CREATE;
    const EDIT   = PermissionActions::EDIT;
    const VIEW   = PermissionActions::VIEW;
}
