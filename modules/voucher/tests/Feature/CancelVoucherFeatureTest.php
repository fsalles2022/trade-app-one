<?php


namespace Voucher\tests\Feature;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use Voucher\Services\VoucherService;
use Voucher\tests\Fixture\VoucherOperationsFixtures;

class CancelVoucherFeatureTest extends TestCase
{
    private $service;
    private $user;

    protected function setUp()
    {
        parent::setUp();
        $this->service = resolve(VoucherService::class);
    }

    /** @test */
    public function should_successful_cancel_discount_without_chargeback(): void
    {
        $sale               = $this->makeSale();
        $serviceTransaction = $sale->services()->first()->serviceTransaction;
        $serviceSale        = $this->service->cancelWithoutChargeback($serviceTransaction, VoucherOperationsFixtures::metadata());
        $this->assertNull($serviceSale->burned['current']);
    }

    /** @test */
    public function should_throw_exception_on_cancel_discount_error(): void
    {
        $this->expectException(BuildExceptions::class);
        $sale               = $this->makeSale(ServiceStatus::ACCEPTED, Operations::TELECOMMUNICATION, false);
        $serviceTransaction = $sale->services()->first()->serviceTransaction;
        $this->service->cancelWithoutChargeback($serviceTransaction, VoucherOperationsFixtures::metadata());
    }

    /** @test */
    public function should_successful_cancel_trade_in_discount_with_chargeback(): void
    {
        $sale               = $this->makeSale(ServiceStatus::ACCEPTED, Operations::TRADE_IN, true);
        $serviceTransaction = $sale->services()->first()->serviceTransaction;
        $serviceSale        = $this->service->cancelWithChargeback($serviceTransaction, VoucherOperationsFixtures::metadata());
        $this->assertEquals(ServiceStatus::CANCELED, $serviceSale->status);
        $this->assertNull($serviceSale->burned['current']);
    }

    /** @test */
    public function should_successful_cancel_triangulation_discount_with_chargeback(): void
    {
        $sale               = $this->makeSale(ServiceStatus::ACCEPTED, Operations::TELECOMMUNICATION, true, true);
        $serviceTransaction = $sale->services()->first()->serviceTransaction;
        $serviceSale        = $this->service->cancelWithChargeback($serviceTransaction, VoucherOperationsFixtures::metadata());
        $this->assertEquals(ServiceStatus::ACCEPTED, $serviceSale->status);
        $this->assertNull($serviceSale->burned['current']);
        $this->assertNull($serviceSale->discount);
    }

    private function makeSale(
        $status = ServiceStatus::ACCEPTED,
        $sector = Operations::TELECOMMUNICATION,
        $burned = true,
        $withDiscount = false
    ) {
        $network         = (new NetworkBuilder())->build();
        $pointOfSale     = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $this->user      = (new UserBuilder())->withNetwork($network)->build();
        $serviceInstance = new Service();
        if ($burned) {
            $serviceInstance->burned = VoucherOperationsFixtures::burnedObject();
        } else {
            $serviceInstance->burned = VoucherOperationsFixtures::notBurnedObject();
        }

        if ($withDiscount) {
            $serviceInstance->discount = $this->makeDiscountSale();
        }

        $serviceInstance->status = $status;
        $serviceInstance->sector = $sector;

        return (new SaleBuilder())
            ->withUser($this->user)
            ->withPointOfSale($pointOfSale)
            ->withServices([$serviceInstance])
            ->build();
    }

    private function makeDiscountSale(): array
    {
        return VoucherOperationsFixtures::discountObject();
    }
}
