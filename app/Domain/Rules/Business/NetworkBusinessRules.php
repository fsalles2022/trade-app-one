<?php

namespace TradeAppOne\Domain\Rules\Business;

use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Domain\Services\RoleService;
use TradeAppOne\Exceptions\SystemExceptions\HierarchyExceptions;
use TradeAppOne\Exceptions\SystemExceptions\NetworkExceptions;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;

class NetworkBusinessRules
{
    protected $roleService;
    protected $pointOfSaleService;

    protected $rolesAuthorized;
    protected $pointOfSalesAuthorized;
    protected $hierarchiesAuthorized;

    protected $network;

    public function __construct(RoleService $roleService, PointOfSaleService $pointOfSaleService)
    {
        $this->roleService        = $roleService;
        $this->pointOfSaleService = $pointOfSaleService;
    }

    public function belongsToPointOfSale(string $cnpj): ?NetworkBusinessRules
    {
        $pointOfSale = $this->pointOfSalesAuthorized->where('cnpj', $cnpj)->first();

        if (is_null($pointOfSale)) {
            $this->pointOfSaleService->findOneByCnpj($cnpj);
            throw UserExceptions::userNotBelongsToPointOfSale();
        }

        if ($this->network->id == $pointOfSale->network->id) {
            return $this;
        }

        throw NetworkExceptions::notBelongsToPointOfSale();
    }

    public function belongsToHierarchy(string $hierarchySlug): ?NetworkBusinessRules
    {
        $hierarchy = $this->hierarchiesAuthorized->where('slug', $hierarchySlug)->first();

        throw_if(is_null($hierarchy), HierarchyExceptions::notFound());
        throw_if(is_null($hierarchy->network), HierarchyExceptions::withoutNetwork());

        if ($this->network->id == $hierarchy->network->id) {
            return $this;
        }

        throw NetworkExceptions::notBelongsToHierarchy();
    }

    public function setNetworkByRoleSlug(string $roleSlug): NetworkBusinessRules
    {
        $role = $this->rolesAuthorized->where('slug', $roleSlug)->first();

        if (is_null($role)) {
            $this->roleService->findOneBySlug($roleSlug);
            throw UserExceptions::userNotPermissionUnderRole($roleSlug);
        }

        $this->network = $role->network;
        return $this;
    }

    public function setRoles($roles): NetworkBusinessRules
    {
        $this->rolesAuthorized = $roles;
        return $this;
    }

    public function setPointOfSales($pointOfSales): NetworkBusinessRules
    {
        $this->pointOfSalesAuthorized = $pointOfSales;
        return $this;
    }

    public function setHierarchies($hierarchies): NetworkBusinessRules
    {
        $this->hierarchiesAuthorized = $hierarchies;
        return $this;
    }

    public function setNetwork(Network $network): NetworkBusinessRules
    {
        $this->network = $network;
        return $this;
    }
}
