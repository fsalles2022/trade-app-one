<?php


namespace Outsourced\ViaVarejo\tests\Unit;

use Outsourced\ViaVarejo\Adapters\Request\ActivationAdapter;
use Outsourced\ViaVarejo\Adapters\Request\MigrationAdapter;
use Outsourced\ViaVarejo\Adapters\Request\PortabilityAdapter;
use Outsourced\ViaVarejo\DataTransferObjects\ViaVarejoBase;
use Outsourced\ViaVarejo\Hooks\ViaVarejoHooks;
use Outsourced\ViaVarejo\Models\ViaVarejo;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHooksFactory;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class ViaVarejoHooksTest extends TestCase
{
    /** @test */
    public function should_assert_if_hooks(): void
    {
        $network = (new NetworkBuilder())->withSlug('via-varejo')->build();
        $hooks   = NetworkHooksFactory::hookExists($network->slug);

        $this->assertTrue($hooks);
    }

    /** @test */
    public function should_return_an_instance_of_via_activation_adapter(): void
    {
        $activationAdapter = $this->getInstanceOfClassByMode('ACTIVATION');

        $this->assertInstanceOf(ActivationAdapter::class, $activationAdapter);
    }

    /** @test */
    public function should_return_an_instance_of_via_migration_adapter(): void
    {
        $activationAdapter = $this->getInstanceOfClassByMode('MIGRATION');

        $this->assertInstanceOf(MigrationAdapter::class, $activationAdapter);
    }

    /** @test */
    public function should_return_an_instance_of_via_portability_adapter(): void
    {
        $activationAdapter = $this->getInstanceOfClassByMode('PORTABILITY');

        $this->assertInstanceOf(PortabilityAdapter::class, $activationAdapter);
    }

    protected function getInstanceOfClassByMode(string $mode): ViaVarejoBase
    {
        $network     = (new NetworkBuilder())->withSlug('via-varejo')->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $service     = factory(ViaVarejo::class)->make();

        $service->mode = $mode;

        $sale = (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices($service)
            ->build();

        return resolve(ViaVarejoHooks::class)->makeAdapter($sale->services()->first());
    }
}
