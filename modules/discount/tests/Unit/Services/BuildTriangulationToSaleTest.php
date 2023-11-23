<?php

namespace Discount\Tests\Unit\Services;

use Discount\Services\BuildTriangulationToSale;
use Discount\Tests\Helpers\Builders\DiscountBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class BuildTriangulationToSaleTest extends TestCase
{
    use ArrayAssertTrait;

    /** @test */
    public function should_return_correct_service_when_device_not_exist()
    {
        $service = [
            'discount' => [],
            'operator' => Operations::CLARO
        ];

        $received = $this->service()->apply($service);
        $this->assertEquals($service, $received);
    }

    /** @test */
    public function should_return_correct_service_when_has_not_discount()
    {
        $service = [
            'device' => [],
            'operator' => Operations::CLARO
        ];

        $received = $this->service()->apply($service);
        $this->assertEquals($service, $received);
    }

    /** @test */
    public function should_return_correct_service_when_has_not_telecommunication()
    {
        $service = [
            'device' => [],
            'discount' => []
        ];

        $received = $this->service()->apply($service);
        $this->assertEquals($service, $received);
    }

    /** @test */
    public function should_return_correct_service_adapted()
    {
        $user     = (new UserBuilder())->build();
        $network  = $user->getNetwork();
        $discount = (new DiscountBuilder())
            ->available($network)
            ->withUser($user)
            ->build();

        $this->actingAs($user);

        $service = [
            'device' => ['id' => $discount->devices->first()->id],
            'operator' => Operations::CLARO,
            'discount' => ['id' => $discount->id]
        ];

        $received = $this->service()->apply($service);

        $this->assertArrayStructure($received, $this->correctStructure());
    }

    private function correctStructure(): array
    {
        return [
            'device' => ['id', 'sku', 'discount', 'priceWith', 'priceWithout'],
            'discount' => ['id', 'title', 'discount']
        ];
    }
    private function service(): BuildTriangulationToSale
    {
        return resolve(BuildTriangulationToSale::class);
    }
}
