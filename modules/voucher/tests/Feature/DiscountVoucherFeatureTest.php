<?php


namespace Voucher\tests\Feature;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;
use TradeAppOne\Features\Customer\Customer;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use Voucher\Services\VoucherService;
use Voucher\tests\Fixture\VoucherOperationsFixtures;

class DiscountVoucherFeatureTest extends TestCase
{
    private $service;
    private $user;

    protected function setUp()
    {
        parent::setUp();
        $this->service = resolve(VoucherService::class);
    }

    /** @test */
    public function should_check_if_transaction_is_available_discount(): void
    {
        $sale = $this->makeSale(ServiceStatus::ACCEPTED, Operations::TRADE_IN);
        Auth::shouldReceive('user')->once()->andReturn($this->user);
        $discounts = $this->service->checkVoucherIsAvailable($sale->services()->first()->serviceTransaction);
        $this->assertCount(1, $discounts);
    }

    /** @test */
    public function should_return_available_trade_in_discounts(): void
    {
        $this->makeSale(ServiceStatus::ACCEPTED, Operations::TRADE_IN);
        Auth::shouldReceive('user')->once()->andReturn($this->user);
        $discounts = $this->service->availableDiscounts(VoucherOperationsFixtures::customerCpf());
        $this->assertCount(1, $discounts);
    }

    /** @test */
    public function should_return_available_operator_discounts(): void
    {
        $this->makeSale(ServiceStatus::APPROVED, Operations::TELECOMMUNICATION, true);
        Auth::shouldReceive('user')->once()->andReturn($this->user);
        $discounts = $this->service->availableDiscounts(VoucherOperationsFixtures::customerCpf());
        $this->assertCount(1, $discounts);
    }

    private function makeSale(
        $status = ServiceStatus::ACCEPTED,
        $sector = Operations::TELECOMMUNICATION,
        $withDiscount = false
    ) {
        $network                 = (new NetworkBuilder())->build();
        $pointOfSale             = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $this->user              = (new UserBuilder())->withNetwork($network)->build();
        $serviceInstance         = new Service();
        $serviceInstance->status = $status;
        $serviceInstance->sector = $sector;
        $serviceInstance->price  = 59.9;
        if ($withDiscount) {
            $serviceInstance->discount = $this->makeDiscountSale();
        }
        $customer                  = new Customer();
        $customer->cpf             = VoucherOperationsFixtures::customerCpf();
        $serviceInstance->customer = $customer->toArray();

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
