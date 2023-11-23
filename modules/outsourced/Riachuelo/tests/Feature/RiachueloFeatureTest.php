<?php

namespace Outsourced\Riachuelo\tests\Feature;

use Illuminate\Http\Response;
use Outsourced\Riachuelo\tests\RiachueloEnumTest;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class RiachueloFeatureTest extends TestCase
{
    use AuthHelper;

    private const ROUTE = '/outsourced/';

    /** @test */
    public function get_should_return_riachuelo_device_by_imei(): void
    {
        $network = factory(Network::class)->create(['slug' => NetworkEnum::RIACHUELO]);
        $user    = (new UserBuilder())->withNetwork($network)->build();

        $this->authAs($user)
            ->get(self::ROUTE . 'devices/identifier/' . RiachueloEnumTest::DEVICE_IMEI)
            ->assertJsonStructure(['sku', 'model', 'label'])
            ->assertStatus(Response::HTTP_OK);
    }
}
