<?php


namespace Voucher\tests\Feature;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use Voucher\Services\VoucherService;
use Voucher\tests\Fixture\VoucherOperationsFixtures;

class BurnVoucherFeatureTest extends TestCase
{
    private $service;
    private $user;

    protected function setUp()
    {
        parent::setUp();
        $this->service = resolve(VoucherService::class);
    }

    /** @test */
    public function should_return_service_from_sale(): void
    {
        $sale               = $this->makeSale();
        $serviceTransaction = $sale->services()->first()->serviceTransaction;
        $serviceSale        = $this->service->getServiceByTransaction($serviceTransaction);
        $this->assertInstanceOf(Service::class, $serviceSale);
    }

    /** @test */
    public function should_throw_exception_when_service_not_found(): void
    {
        $this->expectException(SaleNotFoundException::class);
        $this->service->getServiceByTransaction(VoucherOperationsFixtures::fakeServiceTransaction());
    }

    /** @test */
    public function should_return_true_when_voucher_valid(): void
    {
        $sale = $this->makeSale(ServiceStatus::ACCEPTED, Operations::TRADE_IN);
        Auth::shouldReceive('user')->once()->andReturn($this->user);
        $serviceSale    = $sale->services()->first();
        $resultValidate = $this->service->validateVoucher($serviceSale, VoucherOperationsFixtures::imei());
        $this->assertTrue($resultValidate);
    }

    /** @test */
    public function should_burn_voucher(): void
    {
        $sale          = $this->makeSale(ServiceStatus::ACCEPTED, Operations::TRADE_IN);
        $serviceSale   = $sale->services()->first();
        $burnedService = $this->service->burnVoucher($serviceSale, VoucherOperationsFixtures::imei(), VoucherOperationsFixtures::metadata());
        $this->assertNotNull($burnedService->burned['current']);
    }

    private function makeSale(
        $status = ServiceStatus::ACCEPTED,
        $sector = Operations::TELECOMMUNICATION
    ) {
        $network                 = (new NetworkBuilder())->build();
        $pointOfSale             = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $this->user              = (new UserBuilder())->withNetwork($network)->build();
        $serviceInstance         = new Service();
        $serviceInstance->status = $status;
        $serviceInstance->sector = $sector;

        return (new SaleBuilder())
            ->withUser($this->user)
            ->withPointOfSale($pointOfSale)
            ->withServices([$serviceInstance])
            ->build();
    }
}
