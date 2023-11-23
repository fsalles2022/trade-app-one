<?php

namespace Buyback\Tests\Unit\Services;

use Buyback\Services\WaybillService;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class WaybillPointOfSaleAvailableTest extends TestCase
{
    /** @test */
    public function should_return_none_when_network_and_pointOfSale_without_operation()
    {
        $user = (new UserBuilder())->build();

        $received = $this->service()->getPointOfSaleAvailableService($user, ['FAKE-OPERATORS']);

        $this->assertCount(0, $received);
    }

    /** @test */
    public function should_return_one_when_network_contains_operation()
    {
        $user = (new UserBuilder())->build();

        $received = $this->service()->getPointOfSaleAvailableService($user, [Operations::SALDAO_INFORMATICA]);

        $this->assertCount(1, $received);
    }

    /** @test */
    public function should_return_one_when_pointOfSale_contains_operation()
    {
        $network = factory(Network::class)->create([
            'availableServices' => json_encode([])
        ]);

        $pointOfSale = factory(PointOfSale::class)->create([
            'networkId' => $network->id,
            'availableServices' => json_encode([
                Operations::TRADE_IN        => [
                    Operations::TRADE_IN_MOBILE => [
                        Operations::SALDAO_INFORMATICA,
                        Operations::IPLACE
                    ]
                ]
            ])
        ]);

        $user     = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        $received = $this->service()->getPointOfSaleAvailableService($user, [Operations::SALDAO_INFORMATICA]);

        $this->assertCount(1, $received);
    }

    /** @test */
    public function should_return_only_that_contains_operation_and_has_authorization()
    {
        $user = (new UserBuilder())->build();
        (new PointOfSaleBuilder())->generateTimes(2);

        $received = $this->service()->getPointOfSaleAvailableService($user, [Operations::SALDAO_INFORMATICA]);
        $this->assertCount(1, $received);
    }

    private function service(): WaybillService
    {
        return resolve(WaybillService::class);
    }
}
