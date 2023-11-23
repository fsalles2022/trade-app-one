<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Permissions\PermissionsWrapper;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\PermissionRepository;
use TradeAppOne\Domain\Repositories\Collections\RoleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\RoleNotFoundException;
use TradeAppOne\Exceptions\SystemExceptions\RoleExceptions;

class RoleService extends BaseService
{
    protected $roleRepository;
    protected $permissionRepository;

    public function __construct(RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        $this->roleRepository       = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function findOneBySlug(string $slug): Role
    {
        $role = $this->roleRepository->findOneBy('slug', $slug);

        if (! $role instanceof Role) {
            throw new RoleNotFoundException();
        }

        return $role;
    }

    public function show(int $id, $user): Role
    {
        if (($role = $this->roleRepository->findOneBy('id', $id)) === null) {
            throw new RoleNotFoundException();
        }

        $roleAuthorized = $this->roleRepository
            ->getRolesThatUserHasAuthority($user)
            ->contains('slug', $role->slug);

        if ($roleAuthorized) {
            return $role;
        }

        RoleExceptions::USER_HAS_NOT_AUTHORITY_UNDER_ROLE();
    }

    public function findOneBySlugAndNetworkId(string $slug, string $networkId): Role
    {
        $role = $this->roleRepository->findOneWithFilters([
            'slug'      => $slug,
            'networkId' => $networkId
        ]);

        throw_if(! $role instanceof Role, new RoleNotFoundException());

        return $role;
    }

    public function associateUserToRole(User $user, Role $role)
    {
        return $this->roleRepository->associateUserToRole($user, $role);
    }

    public function all(User $user, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $pagination = $this->roleRepository
            ->filter($user, $filters)
            ->with(['network'])
            ->paginate(10);

        $items = $pagination->getCollection();
        $items = PermissionsWrapper::groupRolesByModule($items);

        $pagination->setCollection($items);

        return $pagination;
    }

    public function create(array $roleForm): ?Role
    {
        $roleForm['slug']      = str_slug($roleForm['name'] . ' ' . $roleForm['networkSlug']);
        $roleForm['networkId'] = $this->networkService->findOneBySlug($roleForm['networkSlug'])->id;

        $role = $this->roleRepository->create($roleForm);

        $permissions = [];
        foreach ($roleForm['permissionsSlug'] as $permission) {
            $permissions[] = $this->permissionRepository->findOneBySlug($permission);
        }

        return $this->roleRepository->assignPermissionsToRole($role, $permissions);
    }

    public function update(int $id, array $roleForm, User $userAuth): ?Role
    {
        $roleForm['slug']      = str_slug($roleForm['name'] . ' ' . $roleForm['networkSlug']);
        $roleForm['networkId'] = $this->networkService->findOneBySlug($roleForm['networkSlug'])->id;
        $role                  = $this->roleRepository->find($id);

        throw_if(! ($role instanceof Role), new RoleNotFoundException());

        $role->stringPermissions()->detach();

        $permissions = [];
        foreach (data_get($roleForm, 'permissionsSlug') as $permission) {
            $permissions[] = $this->permissionRepository->findOneBySlug($permission);
        }

        $this->roleRepository->update($role, $roleForm);

        return $this->roleRepository->assignPermissionsToRole($role, $permissions);
    }

    public function roleMakeSales(Role $role): bool
    {
        $permissions      = $role->permissions;
        $permissionString = SubSystemEnum::API . '.' . SalePermission::NAME;
        $actionsInSale    = data_get($permissions, $permissionString, []);
        return array_search(PermissionActions::CREATE, $actionsInSale, true);
    }

    public function rolesThatUserHasAuthority($user): Collection
    {
        return  $this->roleRepository->getRolesThatUserHasAuthority($user);
    }
}
