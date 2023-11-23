<?php

namespace Buyback\Tests\Unit\Repositories;

use Buyback\Repositories\WaybillRepository;
use Buyback\Tests\Helpers\Builders\WaybillBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class WaybillRepositoryTest extends TestCase
{

    /** @test */
    public function should_return_an_instance()
    {
        $class     = $this->getInstance();
        $className = get_class($class);

        $this->assertEquals(WaybillRepository::class, $className);
    }

    /** @test */
    public function should_return_one_device_not_printed()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        (new WaybillBuilder())
            ->withPointOfSale($pointOfSale)
            ->alreadyPrinted()
            ->build();

        (new WaybillBuilder())
            ->withPointOfSale($pointOfSale)
            ->build();

        $class = $this->getInstance();

        $received = $class->findServicesWithWaybillAvailable(collect()->push($pointOfSale), [Operations::SALDAO_INFORMATICA]);

        $this->assertCount(1, $received);
    }

    /** @test */
    public function should_return_only_devices_from_operation_parametrized()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        (new WaybillBuilder())
            ->withPointOfSale($pointOfSale)
            ->withOperation(Operations::SALDAO_INFORMATICA)
            ->build();

        (new WaybillBuilder())
            ->withPointOfSale($pointOfSale)
            ->withOperation(Operations::IPLACE)
            ->build();

        $class = $this->getInstance();

        $received = $class->findServicesWithWaybillAvailable(collect()->push($pointOfSale), [Operations::IPLACE]);

        $this->assertCount(1, $received);
        $this->assertEquals(Operations::IPLACE, $received->first()->services->first()->operation);
    }

    /** @test */
    public function should_persist_waybill_add_waybill_in_service()
    {
        $waybill = (new WaybillBuilder())->build();
        $user    = (new UserBuilder())->build();
        $class   = $this->getInstance();

        $result = $class->persistWaybill($user, $waybill);

        $firstService = $result->services->first()->waybill;
        $this->assertNotNull($result->id);
        $this->assertArrayHasKey('printedAt', $firstService);
        $this->assertArrayHasKey('id', $firstService);
    }

    private function getInstance(): WaybillRepository
    {
        return resolve(WaybillRepository::class);
    }
}
