<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Repositories\Collections\RolePermissionRepository;

class RolePermissionService
{
    protected $rolePermissionRepository;

    public function __construct(RolePermissionRepository $rolePermissionRepository)
    {
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function removeDuplicate(Collection $rolesPermissions)
    {
        $entriesDeleted          = collect();
        $rolesPermissionsGroupBy = $rolesPermissions->groupBy('roleId');
        foreach ($rolesPermissionsGroupBy as $rolePermissions) {
            $rolePermissionsUnique = $rolePermissions->unique('permissionsId');
            $rolePermissionsDupes  = $rolePermissions->diff($rolePermissionsUnique);
            foreach ($rolePermissionsDupes as $rolePermission) {
                $entriesDeleted->push($rolePermission);
                $this->rolePermissionRepository->delete($rolePermission);
            }
        }
        return $entriesDeleted;
    }
}
