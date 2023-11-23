<?php

namespace ClaroBR\Tests\Unit\Adapters;

use ClaroBR\Adapters\TradeAppToSivAdapter;
use ClaroBR\Models\ControleBoleto;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\Helpers\SivIntegrationHelper;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Tests\TestCase;

class ControleBoletoRequestsTest extends TestCase
{
    use SivFactoriesHelper, SivIntegrationHelper;

    /** @test */
    public function should_return_attribute_for_when_model_is_activation_without_portability()
    {
        $controleBoleto = $this->sivFactories()
            ->of(ControleBoleto::class)->states('activation_without_portability')
            ->make()
            ->toArray();

        $pointOfSale = $this->getPointOfSaleWithSivIdentifiers();
        $sale        = factory(Sale::class)->make(['pointOfSale' => $pointOfSale, 'services' => [$controleBoleto]]);
        $adapted     = TradeAppToSivAdapter::adapt($sale->services[0]);

        self::assertTrue(filled($adapted['service']['iccid']));
        self::assertTrue(filled($adapted['service']['vencimento_id']));
        self::assertTrue(filled($adapted['customer']['nome']));
        self::assertTrue(filled($adapted['customer']['cpf']));
        self::assertTrue(filled($adapted['customer']['email']));
    }

    /** @test */
    public function should_return_attribute_for_when_model_is_activation_and_portability()
    {
        $controleBoleto = $this->sivFactories()
            ->of(ControleBoleto::class)->states('activation_with_portability')
            ->make()
            ->toArray();

        $pointOfSale = $this->getPointOfSaleWithSivIdentifiers();
        $sale        = factory(Sale::class)->make(['pointOfSale' => $pointOfSale, 'services' => [$controleBoleto]]);
        $adapted     = TradeAppToSivAdapter::adapt($sale->services[0]);
        self::assertTrue(filled($adapted['service']['iccid']));
        self::assertTrue(filled($adapted['service']['vencimento_id']));
        self::assertTrue(filled($adapted['service']['portabilidade']));
        self::assertTrue(filled($adapted['customer']['nome']));
        self::assertTrue(filled($adapted['customer']['cpf']));
        self::assertTrue(filled($adapted['customer']['email']));
    }

    /** @test */
    public function should_return_attribute_for_when_model_is_migration_and_new_chip()
    {
        $controleBoleto = $this->sivFactories()
            ->of(ControleBoleto::class)->states('migration_with_chip')
            ->make()
            ->toArray();

        $pointOfSale = $this->getPointOfSaleWithSivIdentifiers();
        $sale        = factory(Sale::class)->make(['pointOfSale' => $pointOfSale, 'services' => [$controleBoleto]]);
        $adapted     = TradeAppToSivAdapter::adapt($sale->services[0]);
        self::assertTrue(filled($adapted['service']['iccid']));
        self::assertTrue(filled($adapted['service']['msisdn']));
        self::assertTrue(filled($adapted['service']['vencimento_id']));
        self::assertTrue(filled($adapted['service']['portabilidade']));
        self::assertTrue(filled($adapted['service']['ddd']));

        self::assertTrue(filled($adapted['customer']['nome']));
        self::assertTrue(filled($adapted['customer']['cpf']));
        self::assertTrue(filled($adapted['customer']['email']));
    }

    /** @test */
    public function should_return_attribute_for_when_model_is_migration()
    {
        $controleBoleto = $this->sivFactories()
            ->of(ControleBoleto::class)->states('migration_with_chip')
            ->make()
            ->toArray();

        $pointOfSale = $this->getPointOfSaleWithSivIdentifiers();
        $sale        = factory(Sale::class)->make(['pointOfSale' => $pointOfSale, 'services' => [$controleBoleto]]);
        $adapted     = TradeAppToSivAdapter::adapt($sale->services[0]);
        self::assertTrue(filled($adapted['service']['msisdn']));
        self::assertTrue(filled($adapted['service']['vencimento_id']));
        self::assertTrue(filled($adapted['service']['ddd']));

        self::assertTrue(filled($adapted['customer']['nome']));
        self::assertTrue(filled($adapted['customer']['cpf']));
        self::assertTrue(filled($adapted['customer']['email']));
    }


    /** @test */
    public function return_without_dependent()
    {
        $controleBoletoService = $this->sivFactories()->of(ControleBoleto::class)
            ->states('migration_with_chip')
            ->make();
        $adapted               = TradeAppToSivAdapter::adapt($controleBoletoService);

        self::assertArrayNotHasKey('dependentes', $adapted['service']);
    }
}
