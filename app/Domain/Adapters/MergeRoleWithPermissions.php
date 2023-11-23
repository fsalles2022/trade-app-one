<?php

namespace TradeAppOne\Domain\Adapters;

use Illuminate\Support\Collection;

class MergeRoleWithPermissions implements Adapter
{

    protected $roles;

    public function __construct(Collection $roles)
    {
        $this->roles = $roles;
    }

    public function adapt(): array
    {
        $rolesWithPermissions = $this->roles->map(function ($role) {
            $permissions = $role->stringPermissions;

            $permissionFiltered = $permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'label' => $permission->label,
                    'slug' => $permission->slug
                ];
            })->toArray();

            return [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'network' => $this->getNetwork($role),
                'permissions' => $permissionFiltered
            ];
        })->toArray();

        return $rolesWithPermissions;
    }

    private function getNetwork($role): array
    {
        $network = $role->network;

        if ($network === null) {
            return [];
        }

        return [
            'id'    => $network->id,
            'slug'  => $network->slug,
            'label' => $network->label,
            'cnpj'  => $network->cnpj
        ];
    }
}
