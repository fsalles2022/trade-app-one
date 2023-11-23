<?php

namespace TradeAppOne\Domain\Adapters;

use Illuminate\Support\Collection;

class NetworksRolesAdapter implements Adapter
{
    protected $roles;
    protected $networks;

    public function __construct(Collection $roles, Collection $networks)
    {
        $this->roles    = $roles;
        $this->networks = $networks;
    }

    public function adapt(): Collection
    {
        $networkRoles = $this->networks->map(function ($network) {
            $roles = $this->roles->where('networkId', '=', $network->id);

            $rolesAdap = $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug
                ];
            })->values()->toArray();

            return [
                'id'    => $network->id,
                'slug'  => $network->slug,
                'label' => $network->label,
                'cnpj'  => $network->cnpj,
                'roles' => $rolesAdap
            ];
        });

        return $networkRoles;
    }
}
