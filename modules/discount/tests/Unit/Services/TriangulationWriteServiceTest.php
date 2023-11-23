<?php

namespace Discount\Tests\Unit\Services;

use Discount\Exceptions\DiscountExceptions;
use Discount\Models\Discount;
use Discount\Services\TriangulationWriteService;
use Discount\Tests\Helpers\Builders\DiscountBuilder;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class TriangulationWriteServiceTest extends TestCase
{
    /** @test */
    public function should_respond_correctly_according_to_the_parameters_informed(): void
    {
        $user    = (new UserBuilder())->build();
        $network = (new NetworkBuilder())->build();
        $pdvs    = $user->pointsOfSale;
        $startAt = now()->subDay();
        $endAt   = now()->addWeek()->endOfDay();
        $device  = factory(DeviceOutSourced::class)->create(['networkId' => $network->id]);

        $discount = (new DiscountBuilder())
            ->withUser($user)
            ->withDevice($device)
            ->withNetwork($network)
            ->withPointOfSale($pdvs->first())
            ->startAt($startAt)
            ->endAt($endAt)
            ->filterModeChosen()
            ->build();

        $discount->pointsOfSale()->sync($pdvs->pluck('id'));
        $product        = $discount->products->first()->toArray();
        $productArray[] = [
            'operator' => data_get($product, 'operator'),
            'operations' => [data_get($product, 'operation')]
        ];

        $attributes = [
            'products' => $product,
            'startAt' => now(),
            'devices' => [['ids' => [4959]]]
        ];

        $response = $this->discountService()->notExistsDiscountWithDevice(
            collect($pdvs),
            $attributes
        );
        $this->assertEquals(true, $response);
    }

    // TODO fix this test, ClaroBR\Exceptions\SivUnavailableException
    public function should_update_discount_points_of_sale(): void
    {
        $network     = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $hierarchy   = (new HierarchyBuilder())->withNetwork($network)->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchy)->build();
        $user        = (new UserBuilder())
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->withHierarchy($hierarchy)
            ->build();
        $device      = factory(DeviceOutSourced::class)->create(['networkId' => $network->id]);
        $startAt     = now()->subDay();
        $endAt       = now()->addWeek()->endOfDay();

        $discount = (new DiscountBuilder())
            ->withUser($user)
            ->withDevice($device)
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale->first())
            ->startAt($startAt)
            ->endAt($endAt)
            ->filterModeChosen()
            ->build();

        $discount->pointsOfSale()->sync($pointOfSale->pluck('id'));
        $devices[] = [
            'ids' => [
                $device->id
            ],
            'discount' => 100
        ];

        $product = $discount->products->first()->toArray();

        $productArray[] = [
            'operator' => data_get($product, 'operator'),
            'operations' => [data_get($product, 'operation')]
        ];
        $pdv            = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchy)->build();

        /** @var Discount $response */
        $response = $this->discountService()->update(
            $user,
            $discount->id,
            [
                'devices' => $devices,
                'filterMode' => 'CHOSEN',
                'pointsOfSale' => [
                    $pdv->cnpj
                ],
                'products' => $productArray
            ]
        );
        $this->assertNotEquals($discount->pointsOfSale, $response->pointsOfSale);
        $this->assertEquals($pdv->cnpj, $response->pointsOfSale->first()->cnpj);
    }

    private function discountService(): TriangulationWriteService
    {
        return resolve(TriangulationWriteService::class);
    }
}
