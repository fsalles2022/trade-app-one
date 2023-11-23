<?php


namespace Voucher\tests\Feature;

use Discount\Tests\Helpers\Builders\DiscountBuilder;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Outsourced\Riachuelo\tests\RiachueloEnumTest;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Features\Customer\Customer;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use Voucher\Exceptions\VoucherExceptions;
use Voucher\Services\VoucherService;
use Voucher\tests\Fixture\VoucherOperationsFixtures;

class ChangeDiscountVoucherFeatureTest extends TestCase
{

    private $service;
    private $user;
    private $network;
    private $filters;
    private $device;

    protected function setUp()
    {
        parent::setUp();
        $this->service = resolve(VoucherService::class);
        $this->makeNetwork();
        $this->makeUser();
    }

    /** @test */
    public function should_throw_exception_when_imei_have_no_triangulation(): void
    {
        $this->expectException(BuildExceptions::class);
        $this->expectExceptionMessage(trans('voucher::exceptions.' . VoucherExceptions::NO_TRIANGULATION_FOR_IMEI));

        $this->makeTriangulation();
        $this->filters['operator']  = Operations::CLARO;
        $this->filters['operation'] = Operations::CLARO_CONTROLE;
        $sale                       = $this->makeSale(
            ServiceStatus::ACCEPTED,
            Operations::TELECOMMUNICATION,
            true
        );
        Auth::shouldReceive('user')->times(2)->andReturn($this->user);
        $this->service->getNewDiscountDevice($sale->services->first()->serviceTransaction, VoucherOperationsFixtures::validImeiForTesting());
    }

    /** @test */
    public function should_return_new_discount_for_imei(): void
    {
        $this->makeTriangulation();
        $sale = $this->makeSale(
            ServiceStatus::ACCEPTED,
            Operations::TELECOMMUNICATION,
            null,
            null,
            true
        );
        Auth::shouldReceive('user')->times(2)->andReturn($this->user);
        $discount = $this->service->getNewDiscountDevice($sale->services->first()->serviceTransaction, VoucherOperationsFixtures::validImeiForTesting());
        $this->assertArrayHasKey('current', $discount);
        $this->assertArrayHasKey('new', $discount);
        $this->assertNotEmpty($discount['new']);
    }

    /** @test */
    public function should_apply_discount_for_new_device(): void
    {
        $this->makeTriangulation();
        $sale    = $this->makeSale(
            ServiceStatus::ACCEPTED,
            Operations::TELECOMMUNICATION,
            null,
            null,
            true
        );
        $service = Mockery::mock(VoucherService::class);
        $service->shouldReceive('applyDiscountForDevice')->andReturn(VoucherOperationsFixtures::newService());
        $discount = $service->applyDiscountForDevice($sale->services->first()->serviceTransaction, VoucherOperationsFixtures::validImeiForTesting());
        $this->assertArrayHasKey('operator', $discount);
        $this->assertArrayHasKey('sector', $discount);
        $this->assertArrayHasKey('plan', $discount);
        $this->assertArrayHasKey('transaction_id', $discount);
        $this->assertArrayHasKey('value', $discount);
        $this->assertArrayHasKey('imei', $discount);
        $this->assertArrayHasKey('model', $discount);
        $this->assertEquals(VoucherOperationsFixtures::validImeiForTesting(), $discount['imei']);
    }

    private function makeNetwork()
    {
        $this->network = (new NetworkBuilder())->withSlug(NetworkEnum::RIACHUELO)->build();
    }

    private function makeUser()
    {
        $this->user = (new UserBuilder())->withNetwork($this->network)->build();
    }

    private function makeSale(
        $status = ServiceStatus::ACCEPTED,
        $sector = Operations::TELECOMMUNICATION,
        $operator = null,
        $operation = null,
        $withDiscount = false
    ) {
        $pointOfSale                = (new PointOfSaleBuilder())->withNetwork($this->network)->build();
        $serviceInstance            = new Service();
        $serviceInstance->status    = $status;
        $serviceInstance->sector    = $sector;
        $serviceInstance->price     = 59.9;
        $serviceInstance->operator  = $operator ?? $this->filters['operator'];
        $serviceInstance->operation = $operation ?? $this->filters['operation'];
        $serviceInstance->product   = $this->filters['product'];
        $serviceInstance->msisdn    = VoucherOperationsFixtures::phone();
        $serviceInstance->mode      = Modes::MIGRATION;
        if ($withDiscount) {
            $serviceInstance->discount = VoucherOperationsFixtures::discountObject();
            $serviceInstance->device   = $this->device->toArray();
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

    private function makeTriangulation()
    {

        $deviceOutsourced = factory(DeviceOutSourced::class)->create([
            'sku' => RiachueloEnumTest::EAN,
            'networkId' => $this->network->id
        ]);

        $this->device = $deviceOutsourced;

        $triangulation = (new DiscountBuilder())
            ->available($this->network)
            ->withDevice($deviceOutsourced)
            ->withPointOfSale($this->user->pointsOfSale->first())
            ->build();

        $product = $triangulation->products->first();

        $this->filters = [
            'operator' => $product->operator,
            'operation' => $product->operation,
            'deviceIdentifier' => VoucherOperationsFixtures::validImeiForTesting(),
            'product' => $product->product
        ];
    }
}
