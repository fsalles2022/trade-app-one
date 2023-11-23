<?php

namespace TradeAppOne\Tests\Unit\Domain\Rules\Business;

use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Rules\Business\UserBusinessRules;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\RoleService;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;
use TradeAppOne\Exceptions\BusinessExceptions\RoleNotFoundException;
use TradeAppOne\Exceptions\SystemExceptions\HierarchyExceptions;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class UserBusinessRulesTest extends TestCase
{
    /** @test */
    public function should_return_instance_when_user_has_permission()
    {
        $permission = 'BUSINESS_RULE';

        $user     = (new UserBuilder())->withPermission($permission)->build();
        $instance = $this->newInstance($user);

        $received = $instance->hasPermission($permission);

        $this->assertInstanceOf(UserBusinessRules::class, $received);
    }

    /** @test */
    public function should_return_exception_when_user_has_not_permission()
    {
        $user     = (new UserBuilder())->build();
        $instance = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.user.' . UserExceptions::UNAUTHORIZED));
        $instance->hasPermission('CREATE');
    }

    /** @test */
    public function should_return_instance_when_user_hasAuthorizationUnderRole()
    {
        $user     = (new UserBuilder())->build();
        $instance = $this->newInstance($user);

        $received = $instance->hasAuthorizationUnderRole($user->role->slug);

        $this->assertInstanceOf(UserBusinessRules::class, $received);
    }

    /** @test */
    public function should_return_exception_when_user_has_not_AuthorizationUnderRole()
    {
        $roleAdmin = (new RoleBuilder())->build();
        $roleUser  = (new RoleBuilder())->withParent($roleAdmin)->build();

        $user     = (new UserBuilder())->withRole($roleUser)->build();
        $instance = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.user.' . UserExceptions::NOT_PERMISSION_UNDER_ROLE, ['role' => $roleUser->slug]));
        $instance->hasAuthorizationUnderRole($roleUser->slug);
    }

    /** @test */
    public function should_return_exception_not_found_when_user_has_not_AuthorizationUnderRole()
    {
        $user     = (new UserBuilder())->build();
        $instance = $this->newInstance($user);

        $this->expectException(RoleNotFoundException::class);
        $instance->hasAuthorizationUnderRole('slug_fake');
    }

    /** @test */
    public function should_return_instance_when_user_hasAuthorizationUnderUser()
    {
        $user     = (new UserBuilder())->build();
        $instance = $this->newInstance($user);

        $received = $instance->hasAuthorizationUnderUser($user);

        $this->assertInstanceOf(UserBusinessRules::class, $received);
    }

    /** @test */
    public function should_return_exception_when_user_has_not_authorizationUnderUser()
    {
        $roleAdmin = (new RoleBuilder())->build();
        $roleUser  = (new RoleBuilder())->withParent($roleAdmin)->build();

        $userAdmin = (new UserBuilder())->withRole($roleAdmin)->build();
        $user      = (new UserBuilder())->withRole($roleUser)->build();
        $instance  = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_USER));
        $instance->hasAuthorizationUnderUser($userAdmin);
    }

    /** @test */
    public function should_return_instance_when_user_hasAuthorizationUnderPointOfSale()
    {
        $user     = (new UserBuilder())->build();
        $instance = $this->newInstance($user);

        $received = $instance->hasAuthorizationUnderPointOfSale($user->pointsOfSale->first()->cnpj);

        $this->assertInstanceOf(UserBusinessRules::class, $received);
    }

    /** @test */
    public function should_return_exception_when_user_has_not_authorizationUnderPointOfSale()
    {
        $pdv      = (new PointOfSaleBuilder())->build();
        $user     = (new UserBuilder())->build();
        $instance = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.user.' . UserExceptions::NOT_BELONGS_TO_POINT_OF_SALE));
        $instance->hasAuthorizationUnderPointOfSale($pdv->cnpj);
    }

    /** @test */
    public function should_return_exception_not_found_point_of_sale_not_exists()
    {
        $user     = (new UserBuilder())->build();
        $instance = $this->newInstance($user);

        $this->expectException(PointOfSaleNotFoundException::class);
        $instance->hasAuthorizationUnderPointOfSale('2314234234234');
    }

    /** @test */
    public function should_return_instance_when_user_hasAuthorizationUnderHierarchy()
    {
        $user      = (new UserBuilder())->build();
        $hierarchy = (new HierarchyBuilder())->withUser($user)->build();
        $instance  = $this->newInstance($user);

        $received = $instance->hasAuthorizationUnderHierarchy($hierarchy->slug);
        $this->assertInstanceOf(UserBusinessRules::class, $received);
    }

    /** @test */
    public function should_return_exception_when_user_has_not_authorizationUnderHierarchy()
    {

        $user      = (new UserBuilder())->build();
        $hierarchy = (new HierarchyBuilder())->build();
        $instance  = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_HIERARCHY));
        $instance->hasAuthorizationUnderHierarchy($hierarchy->slug);
    }

    /** @test */
    public function should_return_exception_not_fund_when_hierarchy_not_exists()
    {
        $user     = (new UserBuilder())->build();
        $instance = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.hierarchy.' . HierarchyExceptions::NOT_FOUND));
        $instance->hasAuthorizationUnderHierarchy('hierarchy_slug');
    }

    /** @test */
    public function should_return_instance_when_hasAuthorizationUnderNetwork()
    {
        $user = (new UserBuilder())->build();

        $received = $this->newInstance($user)->hasAuthorizationUnderNetwork($user->getNetwork()->slug);
        $this->assertInstanceOf(UserBusinessRules::class, $received);
    }

    /** @test */
    public function should_return_exception_when_has_not_authorizationUnderNetwork()
    {
        $user    = (new UserBuilder())->build();
        $network = factory(Network::class)->create();

        $this->expectExceptionMessage(trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_NETWORK));
        $this->newInstance($user)->hasAuthorizationUnderNetwork($network->slug);
    }

    private function newInstance(User $user): UserBusinessRules
    {
        $roleService      = resolve(RoleService::class);
        $hierarchyService = resolve(HierarchyService::class);

        return resolve(UserBusinessRules::class)
            ->setUser($user)
            ->setRoles($roleService->rolesThatUserHasAuthority($user))
            ->setPointOfSales($hierarchyService->getPointsOfSaleThatBelongsToUser($user))
            ->setHierarchies($hierarchyService->hierarchiesThatUserHasAuthority($user));
    }
}
