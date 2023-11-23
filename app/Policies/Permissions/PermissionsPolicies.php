<?php

namespace TradeAppOne\Policies\Permissions;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;

class PermissionsPolicies
{
    public function hasPermission(string $permission)
    {
        if ($user = Auth::user()) {
            return $user->hasPermission($permission);
        }
        return false;
    }

    public function hasPermissionOrAbort(string $permission)
    {
        if ($this->hasPermission($permission)) {
            return true;
        }
        throw UserExceptions::userUnauthorized();
    }
}
