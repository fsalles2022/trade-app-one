<?php

namespace TradeAppOne\Policies;

use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\PointOfSalePermission;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Rules\Business\UserBusinessRules;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;

class PointOfSalePolicy
{
    protected $businessRules;
    protected $pointOfSaleService;

    public function __construct(UserBusinessRules $businessRules, PointOfSaleService $pointOfSaleService)
    {
        $this->businessRules      = $businessRules;
        $this->pointOfSaleService = $pointOfSaleService;
    }

    public function create(User $user, array $data, $pointOfSale): bool
    {
        $permission = PointOfSalePermission::getFullName(PermissionActions::CREATE);

        $this->defaultValidation($user, data_get($data, 'hierarchy.slug'), $pointOfSale, $permission);

        return true;
    }

    public function edit(User $user, array $data, string $cnpj): bool
    {
        $permission  = PointOfSalePermission::getFullName(PermissionActions::EDIT);
        $pointOfSale = $this->pointOfSaleService->findOneByCnpj($cnpj);

        throw_if($pointOfSale === null, PointOfSaleNotFoundException::class);

        $this->defaultValidation($user, data_get($data, 'hierarchy.slug'), $pointOfSale, $permission);

        return true;
    }

    public function export(User $user): bool
    {
        $permissions   = PointOfSalePermission::getFullName(PermissionActions::EXPORT);
        $pointOfSale   = $user->pointsOfSale->first();
        $hierarchySlug = $pointOfSale->hierarchy->slug;

        $this->defaultValidation($user, $hierarchySlug, $pointOfSale, $permissions);

        return true;
    }

    public function defaultValidation($user, $hierarchy, $pointOfSale, $permission): void
    {
        $this->businessRules->setUser($user)
            ->hasPermission($permission);

        if ($hierarchy) {
            $this->businessRules->hasAuthorizationUnderHierarchy($hierarchy);
        }

        if ($pointOfSale) {
            $this->businessRules->hasAuthorizationUnderPointOfSale($pointOfSale->cnpj);
        }
    }
}
