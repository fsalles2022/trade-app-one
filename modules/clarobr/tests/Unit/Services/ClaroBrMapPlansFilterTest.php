<?php

namespace ClaroBR\Tests\Unit\Services;

use ClaroBR\Services\ClaroBrMapPlansFilter;
use ClaroBR\Tests\Helpers\ClaroPosPagoPlansFixture;
use ClaroBR\Tests\Helpers\Fixture\ClaroBoletoPlansFixture;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;

class ClaroBrMapPlansFilterTest extends TestCase
{

    /** @test */
    public function should_filter_return_collection()
    {
        $result = ClaroBrMapPlansFilter::filter(collect(), []);

        $this->assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_return_plan_filtered_by_operation()
    {
        $plansMapped = collect([
            ClaroPosPagoPlansFixture::posPagoMapped(),
            ClaroBoletoPlansFixture::controleBoletoMapped()
        ]);

        $result = ClaroBrMapPlansFilter::filter($plansMapped, [ 'operation' => Operations::CLARO_CONTROLE_BOLETO]);

        $this->assertEquals(1, $result->count());
    }

    /** @test */
    public function should_return_plan_filtered_by_product()
    {
        $plansMapped = collect([
            ClaroPosPagoPlansFixture::posPagoMapped(),
            ClaroBoletoPlansFixture::controleBoletoMapped()
        ]);

        $result = ClaroBrMapPlansFilter::filter($plansMapped, [ 'product' => 67]);

        $this->assertEquals(1, $result->count());
    }

    /** @test */
    public function should_return_plan_filtered_by_all_filters()
    {
        $plansMapped = collect([
            ClaroPosPagoPlansFixture::posPagoMapped(),
            ClaroBoletoPlansFixture::controleBoletoMapped()
        ]);

        $result = ClaroBrMapPlansFilter::filter(
            $plansMapped,
            [
                'product' => 67,
                'operation' => Operations::CLARO_CONTROLE_BOLETO
            ]
        );

        $this->assertEquals(1, $result->count());
    }

    /** @test */
    public function should_return_plan_filtered_by_mode()
    {
        $plansMapped = collect([
            ClaroPosPagoPlansFixture::posPagoMapped(),
            ClaroBoletoPlansFixture::controleBoletoMapped()
        ]);

        $result = ClaroBrMapPlansFilter::filter(
            $plansMapped,
            [
                'mode' => Modes::MIGRATION,
            ]
        );

        $this->assertEquals(1, $result->count());
    }
}
