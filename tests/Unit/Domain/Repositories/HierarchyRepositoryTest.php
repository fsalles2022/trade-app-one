<?php

namespace TradeAppOne\Tests\Unit\Domain\Repositories;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\HierarchyRepository;
use TradeAppOne\Domain\Repositories\Collections\NetworkRepository;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class HierarchyRepositoryTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_hierarchy_repository()
    {
        $hierarchyRepository = new HierarchyRepository(resolve(PointOfSaleRepository::class), resolve(NetworkRepository::class));
        $className = get_class($hierarchyRepository);
        $this->assertEquals(HierarchyRepository::class, $className);
    }

    /** @test */
    public function should_getPointsOfSaleThatBelongsToUser_return_a_collection()
    {
        $user = new User();
        $hierarchyRepository = new HierarchyRepository(resolve(PointOfSaleRepository::class), resolve(NetworkRepository::class));
        $collectionExpected = new Collection();

        $collectionReceived = $hierarchyRepository->getPointsOfSaleThatBelongsToUser($user);

        $this->assertEquals($collectionExpected, $collectionReceived);
    }

    /** @test */
    public function should_return_two_points_of_sale_in_collection_when_has_hierarchy_and_alone_point_of_sale_append()
    {
        $networkOther = factory(Network::class)->create();
        $hierarchy2   = (new HierarchyBuilder())->withNetwork($networkOther)->build();

        $networkTao = factory(Network::class)->create();

        $userTao      = (new UserBuilder())->withNetwork($networkTao)->build();
        $hierarchyTao = (new HierarchyBuilder())->withParent($hierarchy2)->withNetwork($networkOther)->withUser($userTao)->build();
        $pointOfSale1 = (new PointOfSaleBuilder())->withHierarchy($hierarchyTao)->withNetwork($networkTao)->build();

        $pointOfSale2 = (new PointOfSaleBuilder())->withUser($userTao)->withNetwork($networkTao)->build();

        $hierarchyRepository = new HierarchyRepository(resolve(PointOfSaleRepository::class), resolve(NetworkRepository::class));
        $cnpjExpected = new Collection([$pointOfSale1->cnpj, $pointOfSale2->cnpj]);
        $collectionReceived = $hierarchyRepository->getPointsOfSaleThatBelongsToUser($userTao);
        $cnpjReceived = $collectionReceived->pluck('cnpj');

        $intersectedCollection = $cnpjExpected->intersect($cnpjReceived);

        $this->assertEquals(2, $intersectedCollection->count());
    }

    /** @test */
    public function should_return_two_in_collection_when_has_two_points_of_sale_in_system_of_different_hierarchies()
    {
        $networkTao = factory(Network::class)->create();
        $userTao    = (new UserBuilder())->withNetwork($networkTao)->build();
        $hierarchy1 = (new HierarchyBuilder())->withNetwork($networkTao)->withUser($userTao)->build();
        (new PointOfSaleBuilder())->withHierarchy($hierarchy1)->withNetwork($networkTao)->build();

        $networkOther = factory(Network::class)->create();
        $userOther    = (new UserBuilder())->withNetwork($networkOther)->build();

        $hierarchy2   = (new HierarchyBuilder())->withParent($hierarchy1)->withNetwork($networkOther)->withUser($userOther)->build();
        $pointOfSale2 = (new PointOfSaleBuilder())->withHierarchy($hierarchy2)->withNetwork($networkOther)->build();

        $hierarchyRepository = new HierarchyRepository(resolve(PointOfSaleRepository::class), resolve(NetworkRepository::class));
        $cnpjExpected = $pointOfSale2->cnpj;
        $collectionReceived = $hierarchyRepository->getPointsOfSaleThatBelongsToUser($userOther);
        $cnpjReceived = $collectionReceived->pluck('cnpj')->toArray()[0];

        $this->assertEquals($cnpjExpected, $cnpjReceived);
    }

    /** @test */
    public function should_return_three_point_of_sale_when_user_has_hierarchy_with_children()
    {
        $network = factory(Network::class)->create();
        $user    = (new UserBuilder())->withNetwork($network)->build();

        $pointOfSale     = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $hierarchy_1     = (new HierarchyBuilder())->withPointOfSale($pointOfSale)->build();
        $hierarchy_1_1   = (new HierarchyBuilder())->withParent($hierarchy_1)->withUser($user)->build();
        $hierarchy_1_1_1 = (new HierarchyBuilder())->withParent($hierarchy_1_1)->build();

        (new PointOfSaleBuilder())->withNetwork($network)->withUser($user)->withHierarchy($hierarchy_1_1)->build();
        (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchy_1_1_1)->build();

        $hierarchyRepository = new HierarchyRepository(resolve(PointOfSaleRepository::class), resolve(NetworkRepository::class));

        $collectionReceived = $hierarchyRepository->getPointsOfSaleThatBelongsToUser($user);
        $cnpjsReceived      = $collectionReceived->count();

        $this->assertEquals(3, $cnpjsReceived);
    }

    /** @test */
    public function should_return_one_in_collection_when_has_points_of_sale()
    {
        $network = factory(Network::class)->create();
        $user    = (new UserBuilder())->build();

        $hierarchy1 = (new HierarchyBuilder())->withNetwork($network)->build();
        (new PointOfSaleBuilder())->withHierarchy($hierarchy1)->withNetwork($network)->build();

        $hierarchy2   = (new HierarchyBuilder())->withParent($hierarchy1)->withUser($user)->withNetwork($network)->build();
        $pointOfSale2 = (new PointOfSaleBuilder())->withHierarchy($hierarchy2)->withNetwork($network)->build();

        $hierarchyRepository = new HierarchyRepository(resolve(PointOfSaleRepository::class), resolve(NetworkRepository::class));
        $cnpjExpected = $pointOfSale2->cnpj;
        $collectionReceived = $hierarchyRepository->getPointsOfSaleThatBelongsToUser($user);
        $cnpjReceived = $collectionReceived->first()->cnpj;

        $this->assertEquals($cnpjExpected, $cnpjReceived);
    }

    /** @test */
    public function should_return_four_points_of_sale_when_hierarchy_parent_is_null()
    {
        $network = factory(Network::class)->create();
        $user    = (new UserBuilder())->build();

        $hierarchy1 = (new HierarchyBuilder())->withUser($user)->withNetwork($network)->build();
        $hierarchy2 = (new HierarchyBuilder())->withUser($user)->withNetwork($network)->build();
        $hierarchy3 = (new HierarchyBuilder())->withUser($user)->withNetwork($network)->build();

        (new PointOfSaleBuilder())->withHierarchy($hierarchy1)->withNetwork($network)->build();
        (new PointOfSaleBuilder())->withHierarchy($hierarchy2)->withNetwork($network)->build();
        (new PointOfSaleBuilder())->withHierarchy($hierarchy3)->withNetwork($network)->build();

        $hierarchyRepository = new HierarchyRepository(resolve(PointOfSaleRepository::class), resolve(NetworkRepository::class));
        $collectionExpected = 4;
        $collectionReceived = $hierarchyRepository->getPointsOfSaleThatBelongsToUser($user);

        $this->assertEquals($collectionExpected, $collectionReceived->count());
    }

    /** @test */
    public function should_return_one_network_when_call_get_network_that_belongs_to_user()
    {
        $network = factory(Network::class)->create();
        $user    = (new UserBuilder())->withNetwork($network)->build();

        $hierarchyRepository = new HierarchyRepository(resolve(PointOfSaleRepository::class), resolve(NetworkRepository::class));

        $collectionReceived = $hierarchyRepository->getNetworksThatBelongsToUser($user);
        $networksReceived = $collectionReceived->count();

        $this->assertEquals(1, $networksReceived);
    }

    /** @test */
    public function should_return_two_network_when_call_get_network_that_belongs_to_user()
    {
        $network  = factory(Network::class)->create();
        $network2 = factory(Network::class)->create();
        $user     = (new UserBuilder())->withNetwork($network2)->build();

        (new PointOfSaleBuilder())->withUser($user)->withNetwork($network)->build();
        $hierarchy = (new HierarchyBuilder())->withNetwork($network)->build();
        (new HierarchyBuilder())->withUser($user)->withNetwork($network)->withParent($hierarchy)->build();

        $hierarchyRepository = new HierarchyRepository(resolve(PointOfSaleRepository::class), resolve(NetworkRepository::class));

        $collectionReceived = $hierarchyRepository->getNetworksThatBelongsToUser($user);
        $networksReceived   = $collectionReceived->count();

        $this->assertEquals(2, $networksReceived);
    }
}
