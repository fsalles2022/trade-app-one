<?php


namespace Generali\tests;

use Generali\Adapters\Request\GeneraliActivationRequestAdapter;
use Generali\Models\Generali;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class GeneraliActivationRequestAdapterTest extends TestCase
{
    /** @test */
    public function should_return_service_array_adapted(): void
    {
        $sale           = $this->buildSale();
        $serviceAdapted = GeneraliActivationRequestAdapter::adapt($sale->services()->first());

        $this->assertTrue(is_array($serviceAdapted));
    }

    /** @test */
    public function should_return_service_adapted_structure_to_send_payload_generali(): void
    {
        $sale           = $this->buildSale();
        $serviceAdapted = GeneraliActivationRequestAdapter::adapt($sale->services()->first());

        $this->assertArrayHasKey('customer', $serviceAdapted);
        $this->assertArrayHasKey('service', $serviceAdapted);
        $this->assertArrayHasKey('payment', $serviceAdapted);
        $this->assertArrayHasKey('pointOfSale', $serviceAdapted);
    }

    public function buildSale(): Sale
    {
        $pointOfSale    = (new PointOfSaleBuilder())->build();
        $serviceFactory = factory(Generali::class)->make();

        return (new SaleBuilder())->withPointOfSale($pointOfSale)->withServices($serviceFactory)->build();
    }
}
