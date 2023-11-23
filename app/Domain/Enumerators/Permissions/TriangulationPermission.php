<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class TriangulationPermission extends BasePermission
{
    const NAME = "TRIANGULATION";

    const VIEW   = PermissionActions::VIEW;
    const CREATE = PermissionActions::CREATE;
    const EDIT   = PermissionActions::EDIT;
    const DELETE = PermissionActions::DELETE;
}
