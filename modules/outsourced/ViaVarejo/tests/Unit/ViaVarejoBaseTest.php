<?php


namespace Outsourced\ViaVarejo\tests\Unit;

use Outsourced\ViaVarejo\Adapters\Request\MigrationAdapter;

use Outsourced\ViaVarejo\Models\ViaVarejo;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class ViaVarejoBaseTest extends TestCase
{
    /** @test */
    public function should_assert_structure_key_payload(): void
    {
        $service = factory(ViaVarejo::class)->make();
        $sale    = (new SaleBuilder())->withServices($service)->build();

        $payload = (new MigrationAdapter($sale->services()->first()))->toArray()['data'][0];

        $this->assertArrayHasKey('ftm', $payload);
        $this->assertArrayHasKey('plano', $payload);
        $this->assertArrayHasKey('cliente', $payload);
        $this->assertArrayHasKey('endereco', $payload);
        $this->assertArrayHasKey('vendedor', $payload);
        $this->assertArrayHasKey('campanha', $payload);
    }
}
