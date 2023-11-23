<?php

namespace Core\HandBooks\Policies;

use Illuminate\Support\Collection;
use Core\HandBooks\Models\Handbook;
use TradeAppOne\Facades\UserPolicies;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\FilterModes;
use Core\HandBooks\Exceptions\HandbookExceptions;
use Core\HandBooks\Enumerators\HandbookPermissions;
use Core\HandBooks\Services\HandbookService;
use TradeAppOne\Domain\Rules\Business\UserBusinessRules;

class HandbookPolicies
{
    protected $networksFilterMode;
    protected $rolesFilterMode;
    protected $networks = [];
    protected $module;
    protected $roles = [];
    protected $user;

    public function validateToCreate()
    {
        $businessRules = UserPolicies::setUser($this->user);
        $permission    = HandbookPermissions::getFullName(HandbookPermissions::CREATE);

        $this->defaultValidation($businessRules, $permission);

        return $this;
    }

    public function validateToUpdate(Handbook $handbook)
    {
        $businessRules = UserPolicies::setUser($this->user);
        $permission    = HandbookPermissions::getFullName(HandbookPermissions::EDIT);

        $businessRules->hasAuthorizationUnderUserAndMe($handbook->user);
        $this->defaultValidation($businessRules, $permission);

        return $this;
    }

    private function defaultValidation(UserBusinessRules $businessRules, string $permission): HandbookPolicies
    {
        $businessRules->hasPermission($permission);
        $this->moduleExists();

        if ($this->networksFilterMode === FilterModes::CHOSEN) {
            $this->networks = $this->validateNetworks($businessRules);
        }

        if ($this->rolesFilterMode === FilterModes::CHOSEN) {
            $this->roles = $this->validateRoles($businessRules);
        }

        return $this;
    }

    private function moduleExists(): bool
    {
        if ($this->module === null) {
            return false;
        }

        if (array_key_exists($this->module, array_flip(HandbookService::MODULES))) {
            return true;
        }

        throw HandbookExceptions::operationNotFound();
    }

    private function validateNetworks(UserBusinessRules $businessRules): Collection
    {
        foreach ($this->networks as $network) {
            $businessRules->hasAuthorizationUnderNetwork($network);
        }

        return $businessRules->getNetworksAuthorized()->whereIn('slug', $this->networks);
    }

    private function validateRoles(UserBusinessRules $businessRules): Collection
    {
        foreach ($this->roles as $role) {
            $businessRules->hasAuthorizationUnderRole($role);
        }

        return $businessRules->getRolesAuthorized()->whereIn('slug', $this->roles);
    }
}
