<?php

namespace NextelBR\Tests\Adapters;

use NextelBR\Adapters\Request\AdhesionRequestAdapter;
use NextelBR\Models\NextelBRControleBoleto;
use NextelBR\Tests\Helpers\NextelBRFactories;
use TradeAppOne\Tests\TestCase;

class AdhesionRequestAdapterTest extends TestCase
{
    use NextelBRFactories;

    /** @test */
    public function should_format_msisdn_removing_55()
    {
        $controleBoleto = $this->factory()->of(NextelBRControleBoleto::class)->states('portability')->make();
        $adapted        = AdhesionRequestAdapter::adapt($controleBoleto);
        preg_match('/^55/', $adapted['msisdnPortabilidade'], $matches);
        self::assertEmpty($matches);
    }

    /** @test */
    public function should_return_with_imei_key()
    {
        $controleBoleto = $this->factory()->of(NextelBRControleBoleto::class)->states('device')->make();
        $adapted        = AdhesionRequestAdapter::adapt($controleBoleto);
        self::assertArrayHasKey('imeiAparelhoAdquirido', $adapted);
    }

    /** @test */
    public function should_return_without_imei_key()
    {
        $controleBoleto = $this->factory()->of(NextelBRControleBoleto::class)->make();
        $adapted        = AdhesionRequestAdapter::adapt($controleBoleto);
        self::assertArrayNotHasKey('imeiAparelhoAdquirido', $adapted);
    }
}
