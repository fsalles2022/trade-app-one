<?php

namespace Reports\Goals;

use Illuminate\Support\Facades\Gate;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\GoalPermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;

class GoalPolicy
{
    public function registerPolicies()
    {
        Gate::define('importGoalOfPointOfSale', function ($user) {
            $permissionKey      = SubSystemEnum::WEB . '.' . GoalPermission::NAME;
            $networkPermissions = data_get($user->role->permissions, $permissionKey, []);
            return in_array(PermissionActions::IMPORT, $networkPermissions);
        });
    }
}
