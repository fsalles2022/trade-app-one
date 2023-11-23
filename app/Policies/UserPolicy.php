<?php

namespace TradeAppOne\Policies;

use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\UserPermission;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Rules\Business\BusinessRules;
use TradeAppOne\Domain\Rules\Business\UserBusinessRules;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;

class UserPolicy extends BasePolicy
{
    protected $businessRules;
    protected $userService;

    public function __construct(BusinessRules $businessRules, UserService $userService)
    {
        $this->businessRules = $businessRules;
        $this->userService   = $userService;
    }

    public function editUser(User $user, array $data, $cpf): bool
    {
        $permission = UserPermission::getFullName(PermissionActions::EDIT);

        $userToEdit = $this->userService->findOneByCpfWithTrashed($cpf);

        throw_if(is_null($userToEdit), UserExceptions::userNotFound());

        $this->defaultValidation($user, $data, $permission)
            ->hasAuthorizationUnderUser($userToEdit);

        return true;
    }

    public function createUser(User $user, array $data): bool
    {
        $permission = UserPermission::getFullName(PermissionActions::CREATE);

        $this->defaultValidation($user, $data, $permission);
        return true;
    }

    private function defaultValidation($user, $data, $permission): UserBusinessRules
    {
        $pointOfSale = data_get($data, 'pointOfSale');
        $hierarchy   = data_get($data, 'hierarchy');
        $role        = data_get($data, 'role');

        $this->businessRules->setAuthorizations($user);

        $userRules = $this->businessRules
            ->user()
            ->hasPermission($permission)
            ->hasAuthorizationUnderRole($role);

        $networkRules = $this->businessRules
            ->network()
            ->setNetworkByRoleSlug($role);

        if ($hierarchy) {
            $userRules->hasAuthorizationUnderHierarchy($hierarchy);
            $networkRules->belongsToHierarchy($hierarchy);
        }

        if ($pointOfSale) {
            $userRules->hasAuthorizationUnderPointOfSale($pointOfSale);
            $networkRules->belongsToPointOfSale($pointOfSale);
        }

        return $userRules;
    }
}
