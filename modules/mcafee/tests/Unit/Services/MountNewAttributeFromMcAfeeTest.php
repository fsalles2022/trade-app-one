<?php

namespace McAfee\Tests\Unit\Services;

use Illuminate\Support\Facades\Auth;
use McAfee\Models\McAfeeMobileSecurity;
use McAfee\Services\MountNewAttributeFromMcAfee;
use McAfee\Tests\Helpers\McAfeeFactoriesHelper;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class MountNewAttributeFromMcAfeeTest extends TestCase
{
    use McAfeeFactoriesHelper, ArrayAssertTrait;

    /** @test */
    public function should_return_a_correct_structure_when_call_get_attributes()
    {
        $network = factory(Network::class)->create(['slug' => 'tradeup-group']);
        $user    = (new UserBuilder())->withNetwork($network)->build();
        auth()->setUser($user);
        $mountNewAttributeFromMcAfee = resolve(MountNewAttributeFromMcAfee::class);
        $service                     = $this->mcAfeeFactories()->of(McAfeeMobileSecurity::class)->make();
        $response                    = $mountNewAttributeFromMcAfee->getAttributes($service->toArray());
        $this->assertArrayStructure($response, ['product', 'quantity', 'label', 'price']);
    }

    /** @test */
    public function should_return_exception()
    {
        $network = factory(Network::class)->create(['slug' => 'tradeup-group']);
        $user    = (new UserBuilder())->withNetwork($network)->build();
        auth()->setUser($user);
        $mountNewAttributeFromMcAfee = resolve(MountNewAttributeFromMcAfee::class);
        $service                     = $this->mcAfeeFactories()
            ->of(McAfeeMobileSecurity::class)
            ->make(['product' => 'InvalidProduct']);
        $this->expectException(ProductNotFoundException::class);
        $mountNewAttributeFromMcAfee->getAttributes($service->toArray());
    }
}
