<?php

namespace TradeAppOne\Policies;

use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\RolePermission;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use TradeAppOne\Domain\Repositories\Collections\RoleRepository;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Domain\Services\RoleService;
use TradeAppOne\Exceptions\BusinessExceptions\RoleNotFoundException;
use TradeAppOne\Exceptions\SystemExceptions\RoleExceptions;

class RolePolicy
{
    use HandlesAuthorization;

    protected $networkService;
    protected $roleRepository;
    protected $roleService;

    const TRADEUP_GROUP = "tradeup-group";

    public function __construct(NetworkService $networkService, RoleRepository $roleRepository, RoleService $roleService)
    {
        $this->networkService = $networkService;
        $this->roleRepository = $roleRepository;
        $this->roleService    = $roleService;
    }

    public function createRole(User $userAuth, array $data)
    {
        $this
            ->userHasPermission(RolePermission::getFullName(PermissionActions::CREATE))
            ->userCanAssignThisPermissionsToRole($userAuth, $data)
            ->parentBelongsToNetwork($userAuth, $data)
            ->userCanAddRoleThisNetwork($userAuth, $data)
            ->userAuthCanAddThisParent($userAuth, $data)
            ->slugIsUniqueToCreate($data);

        return true;
    }

    public function editRole(User $userAuth, string $id, array $data)
    {
        $this
            ->userHasPermission(RolePermission::getFullName(PermissionActions::EDIT))
            ->roleExists($id)
            ->userCanAssignThisPermissionsToRole($userAuth, $data, $id)
            ->userCanAddRoleThisNetwork($userAuth, $data)
            ->userAuthHasAuthorityUnderRole($userAuth, $id)
            ->slugIsUniqueToEdit($data, $id);

        return true;
    }

    public function defineParent(User $userAuth)
    {
        return $userAuth->hasPermission(RolePermission::getFullName(RolePermission::DEFINE_PARENT));
    }

    private function userHasPermission(string $permission): RolePolicy
    {
        if (hasPermissionOrAbort($permission)) {
            return $this;
        }
    }

    private function userCanAssignThisPermissionsToRole(User $userAuth, $data, $roleId = null): RolePolicy
    {
        $permissionsAuthorized = $userAuth->role->stringPermissions;

        if ($roleId) {
            $permissionsRoleEdit   = $this->roleRepository->find($roleId)->stringPermissions;
            $permissionsAuthorized = $permissionsRoleEdit->merge($permissionsAuthorized);
        }

        foreach ($data['permissionsSlug'] as $permission) {
            if (! $permissionsAuthorized->contains('slug', $permission)) {
                RoleExceptions::USER_CAN_NOT_ASSIGN_PERMISSION_TO_ROLE($permission);
            }
        }
        return $this;
    }

    private function userCanAddRoleThisNetwork(User $userAuth, $data): RolePolicy
    {
        $userAuthNetworkId = $userAuth->getNetwork()->id;
        $tradeUpNetworkId  = $this->networkService->findOneBySlug(self::TRADEUP_GROUP)->id;
        $dataNetworkId     = $this->networkService->findOneBySlug($data['networkSlug'])->id;

        if (($userAuthNetworkId == $dataNetworkId) or $userAuthNetworkId == $tradeUpNetworkId) {
            return $this;
        }
        RoleExceptions::USER_NOT_CAN_ADD_ROLE_IN_THIS_NETWORK();
    }

    private function slugIsUniqueToCreate($data): RolePolicy
    {
        $slug = str_slug($data['name'] . ' ' . $data['networkSlug']);
        $role = $this->roleRepository->findOneBy('slug', $slug);

        if ($role instanceof Role) {
            RoleExceptions::ROLE_ALREADY_EXISTS_REGISTERED();
        }
        return $this;
    }

    private function roleExists(int $id): RolePolicy
    {
        $role = $this->roleRepository->find($id);

        if ($role instanceof Role) {
            return $this;
        }
        throw new RoleNotFoundException();
    }

    private function slugIsUniqueToEdit($data, $id)
    {
        $slug = str_slug($data['name'] . ' ' . $data['networkSlug']);
        $role = $this->roleRepository->findOneBy('slug', $slug);

        if ($role === null) {
            return $this;
        }

        if ($role->id == $id) {
            return $this;
        }

        RoleExceptions::ROLE_ALREADY_EXISTS_REGISTERED();
    }

    private function userAuthHasAuthorityUnderRole(User $userAuth, $id): RolePolicy
    {
        $role            = $this->roleRepository->find($id);
        $rolesAuthorized = $this->roleRepository
            ->getRolesThatUserHasAuthority($userAuth)
            ->contains('slug', $role->slug);

        if ($rolesAuthorized) {
            return $this;
        }

        RoleExceptions::USER_HAS_NOT_AUTHORITY_UNDER_ROLE();
    }

    private function userAuthCanAddThisParent(User $userAuth, array $data): RolePolicy
    {
        $idRoleUserAuth   = $userAuth->role->id;
        $parentIdRoleForm = data_get($data, 'parent');

        if ($idRoleUserAuth != $parentIdRoleForm) {
            $rolesAuthorized = $this->roleRepository->getRolesThatUserHasAuthority($userAuth);
            if ($rolesAuthorized->contains('id', $parentIdRoleForm)) {
                return $this;
            }
            RoleExceptions::USER_AUTH_CAN_NOT_ADD_PARENT();
        }
        return $this;
    }

    private function parentBelongsToNetwork(User $userAuth, array $data)
    {
        $roleParent = $this->roleRepository->find($data['parent']);
        $network    = $this->networkService->findOneBySlug($data['networkSlug']);

        if ($roleParent->networkId == $network->id) {
            return $this;
        }
        RoleExceptions::PARENT_NOT_BELONGS_TO_NETWORK();
    }
}
