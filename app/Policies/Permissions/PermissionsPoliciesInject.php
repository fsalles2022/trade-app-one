<?php

function hasPermission($permission): bool
{
    return (new TradeAppOne\Policies\Permissions\PermissionsPolicies())->hasPermission($permission);
}

function hasPermissionOrAbort($permission): bool
{
    return (new TradeAppOne\Policies\Permissions\PermissionsPolicies())->hasPermissionOrAbort($permission);
}
