<?php

namespace TradeAppOne\Domain\Rules\Business;

use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Policies\Authorizations;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;

class UserBusinessRules extends Authorizations
{
    public function hasPermission(string $permission): UserBusinessRules
    {
        if ($this->getUser()->hasPermission($permission)) {
            return $this;
        }

        throw UserExceptions::userUnauthorized();
    }

    public function hasAuthorizationUnderRole(string $roleSlug): ?UserBusinessRules
    {
        $contains = $this->getRolesAuthorized()->contains('slug', $roleSlug);

        if ($contains) {
            return $this;
        }

        $this->roleService->findOneBySlug($roleSlug);
        throw UserExceptions::userNotPermissionUnderRole($roleSlug);
    }

    public function hasAuthorizationUnderUser(User $userToEdit): ?UserBusinessRules
    {
        $authorization = $this->getRolesAuthorized()->contains('slug', $userToEdit->role->slug);

        if ($authorization) {
            return $this;
        }

        throw UserExceptions::userAuthHasNotAuthorizationUnderUser();
    }

    public function hasAuthorizationUnderPointOfSale(string $cnpj): ?UserBusinessRules
    {
        $contains = $this->getPointsOfSaleAuthorized()->contains('cnpj', $cnpj);

        if ($contains) {
            return $this;
        }

        $this->pointOfSaleService->findOneByCnpj($cnpj);
        throw UserExceptions::userNotBelongsToPointOfSale();
    }

    public function hasAuthorizationUnderHierarchy(string $hierarchy): ?UserBusinessRules
    {
        $authorized = $this->getHierarchiesAuthorized()->contains('slug', $hierarchy);

        if ($authorized) {
            return $this;
        }

        $this->hierarchyService->findOneHierarchyBySlug($hierarchy);
        throw UserExceptions::userHasNotAuthorizationUnderHierarchy();
    }

    public function hasAuthorizationUnderNetwork(string $network): UserBusinessRules
    {
        $authorized = $this->getNetworksAuthorized()->contains('slug', $network);

        if ($authorized) {
            return $this;
        }

        throw UserExceptions::hasNotAuthorizationUnderNetwork();
    }

    public function hasAuthorizationUnderUserAndMe(User $user): UserBusinessRules
    {
        $this->getRolesAuthorized()->push($this->getUser()->role);
        return $this->hasAuthorizationUnderUser($user);
    }
}
