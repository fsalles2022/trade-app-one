<?php

namespace VivoBR\Tests\Unit\Adapters;

use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\TestCase;
use VivoBR\Adapters\SunServiceRequestAdapter;
use VivoBR\Models\VivoControle;
use VivoBR\Models\VivoControleCartao;
use VivoBR\Tests\Helpers\VivoFactoriesHelper;

class SunServiceRequestAdapterTest extends TestCase
{
    use VivoFactoriesHelper;

    /** @test */
    public function adapter_should_return_controle_cartao_adapted_when_service_model_sent()
    {
        $service     = $this->sunFactories()->of(VivoControleCartao::class)->make()->toArray();
        $pointOfSale = factory(PointOfSale::class)->make(['providerIdentifiers' => ['sun' => 123]]);
        $user        = factory(User::class)->make(['integrationCredentials' => ['sun' => ['cpf' => 123]]]);
        $sale        = factory(Sale::class)->make([
            'services'    => [$service],
            'pointOfSale' => $pointOfSale,
            'user'        => $user
        ]);

        $adapted = SunServiceRequestAdapter::adapt($sale->services()->first());

        self::assertNotEmpty($adapted['pessoa']['nome']);
        self::assertNotEmpty($adapted['pessoa']['cpf']);
    }

    /** @test */
    public function adapter_should_return_controle_adapted_when_service_model_sent()
    {
        $service = $this->sunFactories()->of(VivoControle::class)->make()->toArray();
        $sale    = factory(Sale::class)->make(['services' => [$service]]);

        $adapted = SunServiceRequestAdapter::adapt($sale->services()->first());

        self::assertNotEmpty($adapted['pessoa']['nome']);
        self::assertNotEmpty($adapted['pessoa']['cpf']);
        self::assertNotEmpty($adapted['pessoa']['dataNascimento']);
        self::assertNotEmpty($adapted['pessoa']['cidade']);
        self::assertNotEmpty($adapted['pessoa']['uf']);
    }

    /** @test */
    public function adapter_should_return_contact_number_is_whitout_country_code_when_service_model_sent()
    {
        $service = $this->sunFactories()->of(VivoControle::class)->make()->toArray();
        $sale    = factory(Sale::class)->make(['services' => [$service]]);

        $adapted = SunServiceRequestAdapter::adapt($sale->services()->first());

        self::assertNotEmpty($adapted['pessoa']['telefone1']);
        self::assertTrue(strlen($adapted['pessoa']['telefone1']) > 10);
    }

    /** @test */
    public function adapter_should_return_ddd_adapted_when_service_with_portedNumber_sent()
    {
        $service = $this->sunFactories()->of(VivoControle::class)->states('portability')->make()->toArray();
        $sale    = factory(Sale::class)->make(['services' => [$service]]);

        $adapted = SunServiceRequestAdapter::adapt($sale->services()->first());

        self::assertNotEmpty($adapted['servicos'][0]['ddd']);
    }

    /** @test */
    public function adapter_should_return_ddd_adapted_when_service_with_msisdn_sent()
    {
        $service = $this->sunFactories()->of(VivoControle::class)->states('msisdn')->make()->toArray();
        $sale    = factory(Sale::class)->make(['services' => [$service]]);

        $adapted = SunServiceRequestAdapter::adapt($sale->services()->first());

        self::assertNotEmpty($adapted['servicos'][0]['ddd']);
    }

    /** @test */
    public function adapter_should_return_ddd_adapted_when_service_with_areaCode_sent()
    {
        $service = $this->sunFactories()->of(VivoControle::class)->states('portability')->make()->toArray();
        $sale    = factory(Sale::class)->make(['services' => [$service]]);

        $adapted = SunServiceRequestAdapter::adapt($sale->services()->first());

        self::assertEquals($adapted['servicos'][0]['ddd'], 12);
        self::assertEquals($adapted['servicos'][0]['tipoServico'], 'ALTA');
    }

    /** @test */
    public function adapter_should_return_alta_adapted_when_service_has_portability()
    {
        $service = $this->sunFactories()->of(VivoControle::class)->states('portability')->make()->toArray();
        $sale    = factory(Sale::class)->make(['services' => [$service]]);

        $adapted = SunServiceRequestAdapter::adapt($sale->services()->first());

        self::assertEquals($adapted['servicos'][0]['tipoServico'], 'ALTA');
        self::assertEquals($adapted['servicos'][0]['portabilidade'], true);
    }

    /** @test */
    public function should_return_telefone1_without_country_code()
    {
        $service = $this->sunFactories()->of(VivoControle::class)->states('portability')->make()->toArray();
        $sale    = factory(Sale::class)->make(['services' => [$service]]);

        $adapted = SunServiceRequestAdapter::adapt($sale->services()->first());

        self::assertNotContains('55', $adapted['pessoa']['telefone1']);
        self::assertNotContains('+55', $adapted['pessoa']['telefone1']);
    }

    /** @test */
    public function should_return_telefone2_without_country_code()
    {
        $service = $this->sunFactories()->of(VivoControle::class)->states('portability')->make()->toArray();
        $sale    = factory(Sale::class)->make(['services' => [$service]]);

        $adapted = SunServiceRequestAdapter::adapt($sale->services()->first());

        self::assertNotContains('55', $adapted['pessoa']['telefone2']);
        self::assertNotContains('+55', $adapted['pessoa']['telefone2']);
    }
}
