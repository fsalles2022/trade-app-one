<?php

namespace TradeAppOne\Domain\Policies;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\ContextEnum;
use TradeAppOne\Domain\Enumerators\Permissions\UserPermission;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\AuthService;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Domain\Services\RoleService;

class Authorizations
{
    protected $rolesAuthorized;
    protected $pointOfSalesAuthorized;
    protected $hierarchiesAuthorized;
    protected $networksAuthorized;
    protected $user;

    protected $pointOfSaleService;
    protected $roleService;
    protected $hierarchyService;
    protected $authService;
    protected $networkService;

    public function __construct(
        PointOfSaleService $pointOfSaleService,
        RoleService $roleService,
        HierarchyService $hierarchyService,
        AuthService $authService,
        NetworkService $networkService
    ) {
        $this->pointOfSaleService = $pointOfSaleService;
        $this->roleService        = $roleService;
        $this->hierarchyService   = $hierarchyService;
        $this->authService        = $authService;
        $this->networkService     = $networkService;
    }

    public function getRolesAuthorized(): Collection
    {
        if (is_null($this->rolesAuthorized)) {
            $this->rolesAuthorized = $this->roleService->rolesThatUserHasAuthority($this->getUser());
        }

        return $this->rolesAuthorized;
    }

    public function getPointsOfSaleAuthorized(): Collection
    {
        if (is_null($this->pointOfSalesAuthorized)) {
            $this->pointOfSalesAuthorized = $this->hierarchyService->getPointsOfSaleThatBelongsToUser($this->getUser());
        }

        return $this->pointOfSalesAuthorized;
    }

    public function getHierarchiesAuthorized(): Collection
    {
        if (is_null($this->hierarchiesAuthorized)) {
            $this->hierarchiesAuthorized = $this->hierarchyService->hierarchiesThatUserHasAuthority($this->getUser());
        }

        return $this->hierarchiesAuthorized;
    }

    public function getNetworksAuthorized(): Collection
    {
        if (is_null($this->networksAuthorized)) {
            $this->networksAuthorized = $this->hierarchyService->getNetworksThatBelongsToUser($this->getUser());
        }

        return $this->networksAuthorized;
    }

    public function getOperatorsHasAuthorized(): array
    {
        $availableServices = $this->authService->getAvailableServices($this->getUser());
        return array_keys(data_get($availableServices, 'LINE_ACTIVATION', []));
    }

    public function getUsersAuthorized(): Builder
    {
        $queryBuilder = User::query();
        $userContext  = $this->getUser()->getUserContext(UserPermission::NAME);

        if ($userContext === ContextEnum::CONTEXT_ALL) {
            return $queryBuilder;
        }

        $pointOfSaleIds = $this->getPointsOfSaleAuthorized()->pluck('id');
        $hierarchiesIds = $this->getHierarchiesAuthorized()->pluck('id');
        $rolesIds       = $this->getRolesAuthorized()->pluck('id');

        $userList = $queryBuilder->where(static function ($query) use ($pointOfSaleIds, $hierarchiesIds) {
            $query->whereHas('pointsOfSale', static function ($query) use ($pointOfSaleIds) {
                $query->whereIn('pointsOfSaleId', $pointOfSaleIds);
            })->orWhereHas('hierarchies', static function ($query) use ($hierarchiesIds) {
                $query->whereIn('hierarchyId', $hierarchiesIds);
            });
        });

        $userList->whereHas('role', static function ($query) use ($rolesIds) {
            $query->whereIn('id', $rolesIds);
        });

        return $userList;
    }

    public function getUser(): User
    {
        if (is_null($this->user)) {
            $this->user = auth()->user();
        }

        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function setRoles($roles)
    {
        $this->rolesAuthorized = $roles;
        return $this;
    }

    public function setPointOfSales($pointOfSales)
    {
        $this->pointOfSalesAuthorized = $pointOfSales;
        return $this;
    }

    public function setHierarchies($hierarchies)
    {
        $this->hierarchiesAuthorized = $hierarchies;
        return $this;
    }
}
