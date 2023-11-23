<?php

namespace ClaroBR\Tests\Unit\Adapters;

use ClaroBR\Adapters\ClaroBrMapPlansMapper;
use ClaroBR\Tests\Helpers\ClaroPosPagoPlansFixture;
use ClaroBR\Tests\Helpers\Fixture\ClaroBoletoPlansFixture;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class ClaroBrMapPlansMapperTest extends TestCase
{
    /** @test */
    public function should_map_return_collection()
    {
        $result = ClaroBrMapPlansMapper::map([]);
        $this->assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_products_return_with_expected_plan_for_pos_pago()
    {
        $plansFromClaro = ClaroPosPagoPlansFixture::posPagoFromClaro();

        $result = ClaroBrMapPlansMapper::map($plansFromClaro)->first()->toArray();

        $expected = ClaroPosPagoPlansFixture::posPagoMapped()->toArray();

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function should_products_be_empty_without_faturas_not_return()
    {
        $plansFromClaro = ClaroPosPagoPlansFixture::posPagoFromClaro();

        $plansFromClaro[0]['faturas'] = null;

        $result = ClaroBrMapPlansMapper::map($plansFromClaro)->first()->toArray();

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function should_products_return_with_expected_plan_for_controle_boleto()
    {
        $plansFromClaro = ClaroBoletoPlansFixture::controleBoletoFromClaro();

        $result = ClaroBrMapPlansMapper::map($plansFromClaro)->first()->toArray();

        $expected = ClaroBoletoPlansFixture::controleBoletoMapped()->toArray();
        $this->assertEquals($expected, $result);
    }
}
