<?php

namespace Discount\tests\Unit\Services;

use Discount\Services\BuildTriangulationToSale;
use Discount\Services\DiscountService;
use Discount\Tests\Helpers\Builders\DiscountBuilder;
use Illuminate\Support\Collection;
use Mockery;
use Outsourced\Crafts\Devices\OutsourcedDeviceDTO;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class DiscountServiceTest extends TestCase
{
    private static function discountService(): DiscountService
    {
        return resolve(DiscountService::class);
    }

    /** @test */
    public function get_should_return_discounts_that_user_has_permission_to_view()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $network     = $pointOfSale->network;

        $roleAdmin = (new RoleBuilder())->withNetwork($network)->build();
        $userAdmin = (new UserBuilder())->withPointOfSale($pointOfSale)->withRole($roleAdmin)->build();

        $roleAux = (new RoleBuilder())->withNetwork($network)->withParent($roleAdmin)->build();
        $userAux = (new UserBuilder())->withPointOfSale($pointOfSale)->withRole($roleAux)->build();

        (new DiscountBuilder())->withUser($userAux)->withPointOfSale($pointOfSale)->build();
        (new DiscountBuilder())->withUser($userAdmin)->withPointOfSale($pointOfSale)->generateDiscountTimes(2);

        $discountsUserAdmin = self::discountService()->filter($userAdmin, [])->get();
        $discountsUserAux   = self::discountService()->filter($userAux, [])->get();

        $this->assertCount(1, $discountsUserAux);
        $this->assertCount(3, $discountsUserAdmin);
    }

    /** @test */
    public function should_return_one_device()
    {
        $user        = (new UserBuilder())->build();
        $pointOfsale = $user->pointsOfSale->first();
        (new DiscountBuilder())->withPointOfSale($pointOfsale)->generateDiscountTimes(1);
        (new DiscountBuilder())->generateDiscountTimes(2);
        $devices = self::discountService()->triangulationsAvailable($user, []);
        self::assertInstanceOf(Collection::class, $devices);
        self::assertEquals(1, $devices->count());
    }

    /** @test */
    public function should_return_one_when_has_2_discounts_in_different_networks()
    {
        $network2 = factory(Network::class)->create();
        $user     = (new UserBuilder())->build();

        $user2 = (new UserBuilder())->withNetwork($network2)->build();

        $pointOfsale  = $user->pointsOfSale->first();
        $pointOfsale2 = $user2->pointsOfSale->first();

        (new DiscountBuilder())->withPointOfSale($pointOfsale)->generateDiscountTimes(1);
        (new DiscountBuilder())->withPointOfSale($pointOfsale2)->generateDiscountTimes(1);
        (new DiscountBuilder())->generateDiscountTimes(2);

        $devices = self::discountService()->triangulationsAvailable($user, []);
        self::assertInstanceOf(Collection::class, $devices);
        self::assertEquals(1, $devices->count());
    }

    /** @test */
    public function should_return_when_end_date_is_tomorrow()
    {
        $user = (new UserBuilder())->build();

        $pointOfsale = $user->pointsOfSale->first();

        (new DiscountBuilder())
            ->endAt(now()->addDay()->endOfDay())
            ->withPointOfSale($pointOfsale)
            ->generateDiscountTimes(1);

        (new DiscountBuilder())
            ->endAt(now()->subDay()->endOfDay())
            ->withPointOfSale($pointOfsale)
            ->generateDiscountTimes(1);


        $devices = self::discountService()->triangulationsAvailable($user, []);
        self::assertInstanceOf(Collection::class, $devices);
        self::assertEquals(1, $devices->count());
    }

    /** @test */
    public function should_not_return_one_when_mode_is_all()
    {
        $user = (new UserBuilder())->build();

        $network = $user->pointsOfSale->first()->network;

        (new DiscountBuilder())
            ->endAt(now()->addDay()->endOfDay())
            ->filterModeAll()
            ->withNetwork($network)
            ->generateDiscountTimes(1);

        (new DiscountBuilder())
            ->endAt(now()->addDay()->endOfDay())
            ->filterModeAll()
            ->withNetwork($network)
            ->generateDiscountTimes(1);


        $devices = self::discountService()->triangulationsAvailable($user, []);
        self::assertInstanceOf(Collection::class, $devices);
        self::assertEquals(2, $devices->count());
    }

    /** @test */
    public function should_return_one_when_mode_is_all_and_distinct_network()
    {
        $user           = (new UserBuilder())->build();
        $anotherNetwork = factory(Network::class)->create();

        $network = $user->pointsOfSale->first()->network;

        (new DiscountBuilder())
            ->endAt(now()->addDay()->endOfDay())
            ->filterModeAll()
            ->withNetwork($network)
            ->generateDiscountTimes(1);

        (new DiscountBuilder())
            ->endAt(now()->addDay()->endOfDay())
            ->filterModeAll()
            ->withNetwork($anotherNetwork)
            ->generateDiscountTimes(1);


        $devices = self::discountService()->triangulationsAvailable($user, []);
        self::assertInstanceOf(Collection::class, $devices);
        self::assertEquals(1, $devices->count());
    }

    /** @test */
    public function should_return_one_when_mode_is_all_and_distinct_network_in_sale_build()
    {
        $user           = (new UserBuilder())->build();
        $anotherNetwork = factory(Network::class)->create();
        $network        = $user->pointsOfSale->first()->network;
        $sameDevice     = factory(DeviceOutSourced::class)->make();
        $sameDevice->network()->associate($network)->save();
        $this->actingAs($user);

        $discount = (new DiscountBuilder())
            ->startAt(now()->subDay())
            ->endAt(now()->addDay()->endOfDay())
            ->filterModeAll()
            ->withNetwork($network)
            ->withDevice($sameDevice)
            ->generateDiscountTimes(1);

        $anotherDiscount = (new DiscountBuilder())
            ->startAt(now()->subDay())
            ->endAt(now()->addDay()->endOfDay())
            ->filterModeAll()
            ->withNetwork($anotherNetwork)
            ->withDevice($sameDevice)
            ->generateDiscountTimes(1);


        $assetDiscount = $discount->first()->id;

        $attributeToSale = resolve(BuildTriangulationToSale::class)->apply([
            'operator' => $discount->first()->products->first()->operator,
            'operation' => $discount->first()->products->first()->operation,
            'discount' => ['id' => $discount->first()->id],
            'device' => ['id' => $discount->first()->devices->first()->id]
        ]);
        self::assertEquals($assetDiscount, $attributeToSale['discount']['id']);
    }

    /** @test */
    public function should_return_devices_belongs_to_pointOfSale_when_filerMode_is_CHOSEN()
    {
        $user        = (new UserBuilder())->build();
        $pointOfsale = $user->pointsOfSale->first();

        (new DiscountBuilder())->filterModeChosen()->withPointOfSale($pointOfsale)->generateDiscountTimes(1);
        (new DiscountBuilder())->filterModeAll()->generateDiscountTimes(1);

        $devices = self::discountService()->triangulationsAvailable($user, []);
        self::assertCount(1, $devices);
    }

    /** @test */
    public function should_return_triangulations_in_sale_when_not_pass_device()
    {
        $user          = (new UserBuilder())->build();
        $triangulation = (new DiscountBuilder())->available($user->getNetwork())->build();

        $filters = [
            'operator' => $triangulation->products->first()->operator,
            'operation' => array($triangulation->products->first()->operation)
        ];

        $received = self::discountService()->triangulationInSale($user, $filters);

        $this->assertCount(1, $received->triangulations);
    }

    /** @test */
    public function should_return_triangulations_in_sale_when_filter_device_is_empty()
    {
        $user          = (new UserBuilder())->build();
        $triangulation = (new DiscountBuilder())->available($user->getNetwork())->build();

        $filters = [
            'operator' => $triangulation->products->first()->operator,
            'operation' => array($triangulation->products->first()->operation)
        ];

        $received = self::discountService()->triangulationInSale($user, $filters);

        $this->assertCount(1, $received->triangulations);
    }

    /** @test */
    public function should_return_triangulations_in_sale_when_pass_device_and_network_without_outsourced_api()
    {
        $user          = (new UserBuilder())->build();
        $triangulation = (new DiscountBuilder())->available($user->getNetwork())->build();

        $filters = [
            'operator' => $triangulation->products->first()->operator,
            'operation' => array($triangulation->products->first()->operation),
            'deviceIdentifier' => '000000000000110'
        ];

        $received = self::discountService()->triangulationInSale($user, $filters);

        $this->assertCount(1, $received->triangulations);
    }

    /** @test */
    public function should_return_empty_triangulations_in_sale_when_sku_nonexistent_in_outsourced_api()
    {
        $user          = (new UserBuilder())->build();
        $triangulation = (new DiscountBuilder())->available($user->getNetwork())->build();

        $service = Mockery::mock(DiscountService::class)->makePartial();
        $service->shouldReceive('getDeviceOutsourced')
            ->andReturn((new OutsourcedDeviceDTO()));

        $filters = [
            'operator' => $triangulation->products->first()->operator,
            'operation' => array($triangulation->products->first()->operation),
            'deviceIdentifier' => '000000000000110'
        ];

        $received = $service->triangulationInSale($user, $filters);

        $this->assertCount(0, $received->triangulations);
        $this->assertEquals(422, $received->status);
        $this->assertEquals(false, $received->setDevice);
    }

    /** @test */
    public function should_return_empty_triangulations_in_sale_when_sku_existent_in_outsourced_api_but_triangulation_nonexistent()
    {
        $user          = (new UserBuilder())->build();
        $triangulation = (new DiscountBuilder())->available($user->getNetwork())->build();

        $dto     = (new OutsourcedDeviceDTO('000000000000110'));
        $service = Mockery::mock(DiscountService::class)->makePartial();
        $service->shouldReceive('getDeviceOutsourced')->andReturn($dto);
        $service->shouldReceive('triangulationsAvailable')->andReturn(collect());

        $filters = [
            'operator' => $triangulation->products->first()->operator,
            'operation' => array($triangulation->products->first()->operation),
            'deviceIdentifier' => '000000000000110'
        ];

        $received = $service->triangulationInSale($user, $filters);

        $this->assertCount(0, $received->triangulations);
        $this->assertEquals(406, $received->status);
        $this->assertEquals(false, $received->setDevice);
    }

    /** @test */
    public function should_return_triangulations_in_sale_when_sku_existent_in_outsourced_api()
    {
        $user          = (new UserBuilder())->build();
        $triangulation = (new DiscountBuilder())->available($user->getNetwork())->build();

        $dto     = (new OutsourcedDeviceDTO('000000000000110'));
        $service = Mockery::mock(DiscountService::class)->makePartial();
        $service->shouldReceive('getDeviceOutsourced')->andReturn($dto);
        $service->shouldReceive('triangulationsAvailable')->andReturn(collect()->push($triangulation));

        $filters = [
            'operator' => $triangulation->products->first()->operator,
            'operation' => array($triangulation->products->first()->operation),
            'deviceIdentifier' => '000000000000110'
        ];

        $received = $service->triangulationInSale($user, $filters);

        $this->assertCount(1, $received->triangulations);
        $this->assertEquals(200, $received->status);
        $this->assertEquals(true, $received->setDevice);
    }
}
