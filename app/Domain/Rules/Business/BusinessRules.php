<?php

namespace TradeAppOne\Domain\Rules\Business;

use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\RoleService;

class BusinessRules
{
    protected $roleService;
    protected $hierarchyService;

    protected $rolesAuthorized;
    protected $pointOfSalesAuthorized;
    protected $hierarchiesAuthorized;
    protected $currentUser;

    public function __construct(RoleService $roleService, HierarchyService $hierarchyService)
    {
        $this->roleService      = $roleService;
        $this->hierarchyService = $hierarchyService;
    }

    public function network(): NetworkBusinessRules
    {
        return resolve(NetworkBusinessRules::class)
            ->setRoles($this->rolesAuthorized)
            ->setPointOfSales($this->pointOfSalesAuthorized)
            ->setHierarchies($this->hierarchiesAuthorized);
    }

    public function user(): UserBusinessRules
    {
        return resolve(UserBusinessRules::class)
            ->setUser($this->currentUser)
            ->setRoles($this->rolesAuthorized)
            ->setPointOfSales($this->pointOfSalesAuthorized)
            ->setHierarchies($this->hierarchiesAuthorized);
    }

    public function setAuthorizations(User $user): BusinessRules
    {
        $this->currentUser            = $user;
        $this->rolesAuthorized        = $this->roleService->rolesThatUserHasAuthority($user);
        $this->pointOfSalesAuthorized = $this->hierarchyService->getPointsOfSaleThatBelongsToUser($user);
        $this->hierarchiesAuthorized  = $this->hierarchyService->hierarchiesThatUserHasAuthority($user);

        return $this;
    }
}
