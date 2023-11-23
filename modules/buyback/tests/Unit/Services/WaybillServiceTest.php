<?php

namespace Buyback\Tests\Unit\Services;

use Buyback\Exceptions\WaybillEmptyException;
use Buyback\Services\WaybillJob;
use Buyback\Services\WaybillService;
use Buyback\Tests\Helpers\Builders\WaybillBuilder;
use Mockery;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Facades\Uniqid;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class WaybillServiceTest extends TestCase
{
    /** @test */
    public function should_return_an_instance(): void
    {
        $class     = $this->service();
        $className = get_class($class);

        $this->assertEquals(WaybillService::class, $className);
    }

    /** @test */
    public function should_generate_and_send_mail_waybill(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $user->pointsOfSale->first();

        (new WaybillBuilder())->withDrawn()->withPointOfSale($pointOfSale)->build();

        $job = Mockery::mock(WaybillJob::class)->makePartial();
        $job->shouldReceive('downloadAsPdf')->once()->andReturn('PDF');
        $this->app->instance(WaybillJob::class, $job);

        $this->service()->generateWaybill($user, $pointOfSale->cnpj, null);
    }

    /** @test */
    public function should_persist_waybill(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $user->pointsOfSale->first();

        $waybill = (new WaybillBuilder())->withDrawn()->withPointOfSale($pointOfSale)->build();

        Uniqid::shouldReceive('generate')->andReturn('12345');
        $this->service()->generateWaybill($user, $pointOfSale->cnpj, null);

        $this->assertDatabaseHas('sales', [
            'services.serviceTransaction' => $waybill->services->first()->serviceTransaction,
            'services.waybill.id'         => '12345'
        ], 'mongodb');
    }

    /** @test */
    public function should_generate_waybill_throw_exception_when_not_has_devices(): void
    {
        (new WaybillBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->make();
        $user        = (new UserBuilder())->build();

        $this->expectException(WaybillEmptyException::class);
        $this->service()->consolidateWaybill($user, $pointOfSale, [Operations::SALDAO_INFORMATICA]);
    }

    /** @test */
    public function should_generate_waybill_create_binary_pdf(): void
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        (new WaybillBuilder())
            ->withDrawn()
            ->withPointOfSale($pointOfSale)
            ->withOperation(Operations::SALDAO_INFORMATICA)
            ->build();

        $user     = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        $waybills = $this->service()->generateWaybill($user, $pointOfSale->cnpj, null);

        $this->assertInternalType('string', $waybills);
    }
    
    /** @test */
    public function should_return_only_services_with_waibill_available(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $user->pointsOfSale->first();

        (new WaybillBuilder())
            ->withPointOfSale($pointOfSale)
            ->withOperation(Operations::SALDAO_INFORMATICA)
            ->build();

        (new WaybillBuilder())
            ->withPointOfSale($pointOfSale)
            ->withOperation(Operations::IPLACE)
            ->build();

        $waybills = $this->service()->getWaybillsAvailable($user)->first();
        $this->assertCount(1, $waybills->services);
    }

    private function service(): WaybillService
    {
        return resolve(WaybillService::class);
    }
}
