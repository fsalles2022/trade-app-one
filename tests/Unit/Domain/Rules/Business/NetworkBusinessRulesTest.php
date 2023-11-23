<?php

namespace TradeAppOne\Tests\Unit\Domain\Rules\Business;

use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Rules\Business\NetworkBusinessRules;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\RoleService;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;
use TradeAppOne\Exceptions\SystemExceptions\HierarchyExceptions;
use TradeAppOne\Exceptions\SystemExceptions\NetworkExceptions;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class NetworkBusinessRulesTest extends TestCase
{
    /** @test */
    public function should_return_exception_when_pointOfSale_not_exists()
    {
        $user         = (new UserBuilder())->build();
        $networkRules = $this->newInstance($user);

        $this->expectException(PointOfSaleNotFoundException::class);
        $networkRules->belongsToPointOfSale('CNPJ-FAKE');
    }

    /** @test */
    public function should_return_exception_when_pointOfSale_not_belongs_to_user()
    {
        $user   = (new UserBuilder())->build();
        $newPDV = (new PointOfSaleBuilder())->build();

        $networkRules = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.user.' . UserExceptions::NOT_BELONGS_TO_POINT_OF_SALE));
        $networkRules->belongsToPointOfSale($newPDV->cnpj);
    }

    /** @test */
    public function should_return_instance_when_network_belongsToPointOfSale()
    {
        $user   = (new UserBuilder())->build();
        $newPDV = (new PointOfSaleBuilder())->withUser($user)->withNetwork($user->getNetwork())->build();

        $networkRules = $this->newInstance($user);

        $received = $networkRules->belongsToPointOfSale($newPDV->cnpj);
        $this->assertInstanceOf(NetworkBusinessRules::class, $received);
    }

    /** @test */
    public function should_return_exception_when_notBelongsToPointOfSale()
    {
        $user   = (new UserBuilder())->build();
        $newPDV = (new PointOfSaleBuilder())->withUser($user)->build();

        $networkRules = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.network.' . NetworkExceptions::NOT_BELONGS_TO_POINT_OF_SALE));
        $networkRules->belongsToPointOfSale($newPDV->cnpj);
    }

    /** @test */
    public function should_return_exception_when_hierarchy_not_exists()
    {
        $user         = (new UserBuilder())->build();
        $networkRules = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.hierarchy.' . HierarchyExceptions::NOT_FOUND));
        $networkRules->belongsToHierarchy('HIERARCHY-FAKE');
    }

    /** @test */
    public function should_return_exception_when_hierarchy_without_network()
    {
        $user         = (new UserBuilder())->build();
        $hierarchy    = (new HierarchyBuilder())->withUser($user)->build();
        $networkRules = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.hierarchy.' . HierarchyExceptions::WITHOUT_NETWORK));
        $networkRules->belongsToHierarchy($hierarchy->slug);
    }

    /** @test */
    public function should_return_instance_when_network_belongsToHierarchy()
    {
        $user      = (new UserBuilder())->build();
        $hierarchy = (new HierarchyBuilder())->withUser($user)->withNetwork($user->getNetwork())->build();

        $networkRules = $this->newInstance($user);

        $received = $networkRules->belongsToHierarchy($hierarchy->slug);
        $this->assertInstanceOf(NetworkBusinessRules::class, $received);
    }

    /** @test */
    public function should_return_exception_when_network_notBelongsToHierarchy()
    {
        $user      = (new UserBuilder())->build();
        $network   = (new NetworkBuilder())->build();
        $hierarchy = (new HierarchyBuilder())->withUser($user)->withNetwork($network)->build();

        $networkRules = $this->newInstance($user);

        $this->expectExceptionMessage(trans('exceptions.network.' . NetworkExceptions::NOT_BELONGS_TO_HIERARCHY));
        $networkRules->belongsToHierarchy($hierarchy->slug);
    }

    private function newInstance(User $user): NetworkBusinessRules
    {
        $roleService      = resolve(RoleService::class);
        $hierarchyService = resolve(HierarchyService::class);

        return resolve(NetworkBusinessRules::class)
            ->setNetwork($user->getNetwork())
            ->setRoles($roleService->rolesThatUserHasAuthority($user))
            ->setPointOfSales($hierarchyService->getPointsOfSaleThatBelongsToUser($user))
            ->setHierarchies($hierarchyService->hierarchiesThatUserHasAuthority($user));
    }
}
