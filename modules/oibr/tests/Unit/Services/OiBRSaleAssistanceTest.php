<?php

namespace OiBR\Tests\Unit\Services;

use OiBR\Assistance\OiBRSaleAssistance;
use OiBR\Assistance\OiControleBoletoAssistance;
use OiBR\Assistance\OiControleCartaoAssistance;
use OiBR\Models\OiBRControleBoleto;
use OiBR\Models\OiBRControleCartao;
use OiBR\Tests\Helpers\OiBRFactories;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Tests\TestCase;

class OiBRSaleAssistanceTest extends TestCase
{
    use OiBRFactories;

    /** @test */
    public function should_return_success_when_controle_cartao()
    {
        $pointOfSale = $this->pointOfSaleOiBR()->toArray();
        $service     = $this->oiBRfactory()->of(OiBRControleCartao::class)->make()->toArray();
        $sale        = factory(Sale::class)->make(['services' => [$service], 'pointOfSale' => $pointOfSale]);

        $mock = \Mockery::mock(OiControleCartaoAssistance::class)->makePartial();
        $mock->shouldReceive('integrateService')->once();
        $this->app->singleton(OiControleCartaoAssistance::class, function () use ($mock) {
            return $mock;
        });

        $assistance = resolve(OiBRSaleAssistance::class);
        $assistance->integrateService($sale->services[0]);
    }


    /** @test */
    public function should_return_success_when_controle_boleto()
    {
        $pointOfSale = $this->pointOfSaleOiBR()->toArray();
        $service     = $this->oiBRfactory()->of(OiBRControleBoleto::class)->make()->toArray();
        $sale        = factory(Sale::class)->make(['services' => [$service], 'pointOfSale' => $pointOfSale]);

        $mock = \Mockery::mock(OiControleBoletoAssistance::class)->makePartial();
        $mock->shouldReceive('integrateService')->once();
        $this->app->singleton(OiControleBoletoAssistance::class, function () use ($mock) {
            return $mock;
        });

        $assistance = resolve(OiBRSaleAssistance::class);
        $assistance->integrateService($sale->services[0]);
    }
}
