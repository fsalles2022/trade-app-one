<?php

namespace TradeAppOne\Domain\Components\Permissions;

use Illuminate\Support\Collection;

class PermissionsWrapper
{
    public static function wrap(?Collection $permissions): array
    {
        $permissionsConverted = [];
        foreach ($permissions as $permission) {
            $nestedArray = [];
            $slug        = strtoupper(data_get($permission, 'slug'));
            $client      = strtoupper(data_get($permission, 'client'));
            array_set($nestedArray, $client . '.' . $slug, 1);
            array_push($permissionsConverted, $nestedArray);
        }

        if (filled($permissionsConverted)) {
            $permissionsConverted = array_merge_recursive(...$permissionsConverted);
        } else {
            $permissionsConverted = [];
        }
        $newPermissions = [];
        foreach ($permissionsConverted as $client => $modules) {
            foreach ($modules as $module => $actions) {
                $moduleActions = [];
                if (is_array($actions)) {
                    $moduleActions = array_keys($actions);
                }
                $newPermissions[$client][$module] = $moduleActions;
            }
        }
        return $newPermissions;
    }

    public static function groupRolesByModule(Collection $roles): Collection
    {
        return $roles->map(function ($role) {
            $role['stringPermission'] = self::groupPermissionsByModule($role->stringPermissions);
            return $role;
        });
    }

    public static function groupPermissionsByModule(Collection $permissions): Collection
    {
        return $permissions->groupBy(function ($eachPermission) {
            return self::getPrefix($eachPermission->slug);
        });
    }

    private static function getPrefix($slug)
    {
        return substr($slug, 0, strpos($slug, "."));
    }
}
