<?php

namespace Buyback\test\Feature;

use Discount\Enumerators\DiscountStatus;
use Discount\Models\DiscountProduct;
use Discount\Tests\Helpers\Builders\DiscountBuilder;
use Illuminate\Http\Response;
use Outsourced\Riachuelo\tests\RiachueloEnumTest;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class DiscountFeatureTest extends TestCase
{
    use AuthHelper;

    protected $endPointDiscounts = 'discounts';

    /** @test */
    public function get_should_response_with_status_200_when_exits_discounts_in_list()
    {
        $userHelper = (new UserBuilder())->build();
        (new DiscountBuilder())->withPointOfSale($userHelper->pointsOfSale->first())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->post('/triangulations/list');

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_return_only_one_when_operator_is_not_equals()
    {
        $userHelper     = (new UserBuilder())->build();
        $product        = factory(DiscountProduct::class)->make(['operator' => Operations::OI]);
        $anotherProduct = factory(DiscountProduct::class)->make(['operator' => Operations::CLARO]);
        (new DiscountBuilder())
            ->withPointOfSale($userHelper->pointsOfSale->first())
            ->withProduct($anotherProduct)
            ->build();
        $discount = (new DiscountBuilder())
            ->withPointOfSale($userHelper->pointsOfSale->first())
            ->withProduct($product)
            ->build();
        $product  = $discount->products->first()->toArray();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->get("$this->endPointDiscounts/available?operator=" . $product['operator']."&operation[]=".$product['operation']);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id']);
        self::assertCount(1, collect(json_encode($response, true)));
    }

    /** @test */
    public function get_should_return_only_one_when_network_is_not_equals_and_operator_equals()
    {
        $network    = factory(Network::class)->create();
        $userHelper = (new UserBuilder())->build();
        $point      = factory(PointOfSale::class)->create(['networkId' => $network->id]);

        $product        = factory(DiscountProduct::class)->make(['operator' => Operations::OI]);
        $anotherProduct = factory(DiscountProduct::class)->make(['operator' => Operations::OI]);

        (new DiscountBuilder())
            ->withPointOfSale($point)
            ->withProduct($anotherProduct)
            ->build();
        $discount = (new DiscountBuilder())
            ->withPointOfSale($userHelper->pointsOfSale->first())
            ->withProduct($product)
            ->build();
        $product  = $discount->products->first()->toArray();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->get("$this->endPointDiscounts/available?operator=" . $product['operator']. "&operation[]=".$product['operation']);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['id']);
        self::assertCount(1, collect(json_encode($response, true)));
    }

    /** @test */
    public function get_should_return_triangulations_in_sale_when_deviceIdentifier_informed()
    {
        $network = (new NetworkBuilder())->withSlug(NetworkEnum::RIACHUELO)->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();

        $deviceOutsourced = factory(DeviceOutSourced::class)->create([
            'sku' => RiachueloEnumTest::EAN,
            'networkId' => $network->id
        ]);

        $triangulation = (new DiscountBuilder())
            ->available($network)
            ->withDevice($deviceOutsourced)
            ->withPointOfSale($user->pointsOfSale->first())
            ->build();

        $product = $triangulation->products->first();

        (new DiscountBuilder())->build();

        $filters = [
            'operator' => $product->operator,
            'operation' => array($product->operation),
            'deviceIdentifier' => RiachueloEnumTest::DEVICE_IMEI
        ];

        $response = $this->authAs($user)
            ->json('GET', "$this->endPointDiscounts/available", $filters);

        $this->assertEquals($triangulation->id, $response->json('triangulations.0.id'));
        $this->assertEquals($deviceOutsourced->id, $response->json('triangulations.0.discount.id'));
        $this->assertEquals($product->id, $response->json('triangulations.0.discount.products.0.id'));

        $response->assertJsonStructure([
            'setDevice',
            'triangulations' => [
                '*' => [
                    'id',
                    'label',
                    'sku',
                    'discount' => [
                        'title',
                        'id',
                        'price',
                        'discount',
                        'products' => [
                            '*' => [
                                'id',
                                'label',
                                'product',
                                'discountId'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
    
    /** @test */
    public function get_should_return_devices_available_to_triangulations()
    {
        $network = (new NetworkBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();

        $triangulation = (new DiscountBuilder())
            ->withUser($user)
            ->withNetwork($network)
            ->filterModeAll()
            ->build();

        (new DiscountBuilder())->build();

        $response = $this->authAs($user)->get("triangulations/devices-available");

        $this->assertCount(1, $response->json());
        $this->assertEquals($triangulation->id, $response->json('0.id'));
    }

    /** @test */
    public function post_should_return_triangulations_to_simulations()
    {
        $user          = (new UserBuilder())->build();
        $triangulation = (new DiscountBuilder())
            ->withUser($user)
            ->withNetwork($user->getNetwork())
            ->filterModeAll()
            ->startAt(now()->subMonth())
            ->endAt(now()->addMonth())
            ->withStatus(DiscountStatus::ACTIVE)
            ->build();

        $deviceId = $triangulation->devices->first()->deviceId;
        (new DiscountBuilder())->build();

        $response = $this->authAs($user)->post("triangulations/simulation", ['deviceId' => $deviceId]);

        $this->assertCount(1, $response->json());
        $response->assertJsonStructure(['*' => [
            'operator', 'operation', 'label', 'discount' => [
                'title','startAt', 'endAt', 'price', 'discount', 'priceWith'
            ]
        ]]);
    }
}
