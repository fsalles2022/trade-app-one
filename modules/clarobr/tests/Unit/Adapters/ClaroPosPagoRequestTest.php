<?php

namespace ClaroBR\Tests\Unit\Adapters;

use ClaroBR\Adapters\TradeAppToSivAdapter;
use ClaroBR\Models\ClaroPos;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use TradeAppOne\Tests\TestCase;

class ClaroPosPagoRequestTest extends TestCase
{
    use SivFactoriesHelper;

    /** @test */
    public function return_with_dependent()
    {
        $controleBoletoService = $this->sivFactories()->of(ClaroPos::class)
            ->states('dependent_with_iccid')
            ->make();
        $adapted               = TradeAppToSivAdapter::adapt($controleBoletoService);

        self::assertNotEmpty($adapted['service']['dependentes']);
    }

    /** @test */
    public function return_with_dependent_and_plano_id_filled()
    {
        $controleBoletoService = $this->sivFactories()->of(ClaroPos::class)
            ->states('dependent_with_iccid')
            ->make();
        $adapted               = TradeAppToSivAdapter::adapt($controleBoletoService);

        self::assertNotEmpty($adapted['service']['dependentes']);
        self::assertNotEmpty($adapted['service']['dependentes'][0]['plano_id']);
    }

    /** @test */
    public function return_with_dependent_and_iccid_and_ported_number_filled()
    {
        $controleBoletoService = $this->sivFactories()->of(ClaroPos::class)
            ->states('dependent_portability')
            ->make();
        $adapted               = TradeAppToSivAdapter::adapt($controleBoletoService);

        self::assertNotEmpty($adapted['service']['dependentes'][0]['portabilidade']);
        self::assertNotEmpty($adapted['service']['dependentes'][0]['iccid']);
    }
}
