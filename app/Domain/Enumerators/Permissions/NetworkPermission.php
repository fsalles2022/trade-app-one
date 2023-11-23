<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class NetworkPermission extends BasePermission
{
    const NAME = 'NETWORK';

    const UPDATE_PREFERENCES = PermissionActions::UPDATE_PREFERENCES;
    const CREATE             = PermissionActions::CREATE;
}
