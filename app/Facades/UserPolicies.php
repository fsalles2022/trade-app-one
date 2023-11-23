<?php

namespace TradeAppOne\Facades;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Rules\Business\UserBusinessRules;

/**
 * @method static UserBusinessRules hasPermission(string $permission)
 * @method static UserBusinessRules hasAuthorizationUnderRole(string $roleSlug)
 * @method static UserBusinessRules hasAuthorizationUnderUser(User $user)
 * @method static UserBusinessRules hasAuthorizationUnderPointOfSale(string $cnpj)
 * @method static UserBusinessRules hasAuthorizationUnderHierarchy(string $hierarchy)
 * @method static UserBusinessRules hasAuthorizationUnderUserAndMe(User $user)
 * @method static UserBusinessRules hasAuthorizationUnderNetwork(string $network)
 * @method static UserBusinessRules setUser(User $user)
 * @method static UserBusinessRules setRoles($roles)
 * @method static UserBusinessRules setPointOfSales($pointsOfSale)
 * @method static UserBusinessRules setHierarchies($hierarchies)
 * @method static Collection getRolesAuthorized()
 * @method static Collection getNetworksAuthorized()
 * @method static Collection getPointsOfSaleAuthorized()
 * @method static Collection getHierarchiesAuthorized()
 * @method static Builder getUsersAuthorized()
 * @method static User getUser()
 * @method static array getOperatorsHasAuthorized()
 *
 * @see UserBusinessRules
 */
class UserPolicies extends Facade
{
    protected static function getFacadeAccessor()
    {
        return UserBusinessRules::class;
    }
}
