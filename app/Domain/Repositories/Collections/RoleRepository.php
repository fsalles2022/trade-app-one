<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Enumerators\RoleEnum;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

class RoleRepository extends BaseRepository
{
    protected $model = Role::class;

    public function findOneWithFilters(array $where): ?Role
    {
        $model = $this->createModel();
        foreach ($where as $key => $value) {
            $model = $model->where($key, '=', $value);
        }
        return $model->first();
    }

    public function associateUserToRole(User $user, Role $role)
    {
        $user->role()->associate($role)->save();
        return $user;
    }

    public function assignPermissionsToRole(Role $role, array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($permission instanceof Permission) {
                $role->stringPermissions()->attach($permission);
            }
        }
        return $role;
    }

    public function findByAuthorizedRoles($user): ?Builder
    {
        $roleUser = $user->role;

        $roles = Role::query();

        if ($roleUser->parentIsNull()) {
            return $roles;
        }

        if ($this->canShowBrother($roleUser)) {
            return $roles->where('sequence', 'like', "{$roleUser->parentInstance->sequence}.%");
        }

        return $roles
            ->where('sequence', 'like', "{$roleUser->sequence}.%")
            ->where('networkId', $roleUser->networkId);
    }

    public function filter(User $user, array $filters)
    {
        $slug     = data_get($filters, 'roleSlug', []);
        $networks = data_get($filters, 'network', []);
        
        $query = $this->findByAuthorizedRoles($user);

        if ($slug) {
            $query->whereIn('slug', $slug);
        }
        if ($networks) {
            $query->whereHas('network', function ($network) use ($networks) {
                $network->whereIn('slug', $networks);
            });
        }
        return $query;
    }

    public function getRolesThatUserHasAuthority($user)
    {
        return $this->findByAuthorizedRoles($user)->get();
    }

    private function canShowBrother($role)
    {
        return in_array($role->slug, RoleEnum::SUPPORT_TRADEUP);
    }
}
