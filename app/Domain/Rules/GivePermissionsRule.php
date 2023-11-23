<?php

namespace TradeAppOne\Domain\Rules;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GivePermissionsRule extends Rule
{
    public function passes($attribute, $values)
    {
        $ownerPermissions = Auth::user()->role->permissions;

        foreach ($values as $plataform => $modules) {
            foreach ($modules as $module => $actions) {
                $permissionsRequested = $modules;
                $count                = array_diff(
                    $permissionsRequested[$module],
                    $ownerPermissions[$plataform][$module]
                );
            }
        }
        return $count ? false : true;
    }

    public function message()
    {
        return trans('validation.permissions');
    }
}
