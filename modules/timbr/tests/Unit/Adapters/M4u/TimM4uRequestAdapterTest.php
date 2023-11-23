<?php

namespace TimBR\Tests\Unit\Adapters\M4u;

use TimBR\Adapters\M4u\TimBRM4uRequestAdapter;
use TimBR\Models\TimBRExpress;
use TimBR\Tests\Helpers\TimFactoriesHelper;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Tests\TestCase;

class TimM4uRequestAdapterTest extends TestCase
{
    use TimFactoriesHelper;

    /** @test */
    public function should_return_new_contract_when_iccid_is_sent()
    {
        $network                          = factory(Network::class)->make(['slug' => 'rede']);
        $pointOfSale                      = factory(PointOfSale::class)->make(['network' => $network]);
        $pointOfSale->providerIdentifiers = json_encode(["TIM" => 'ada']);
        $serviceTim                       = $this->timFactories()
            ->of(TimBRExpress::class)
            ->make()
            ->toArray();
        $sale                             = factory(Sale::class)->make([
            'pointOfSale' => $pointOfSale->toArray(),
            'services'    => [$serviceTim]
        ]);
        $adapted                          = TimBRM4uRequestAdapter::adapt($sale->services[0]);
        self::assertNotEmpty($adapted['client']['creditCard']);
    }

    /** @test */
    public function should_return_new_contract_when_iccid_and_msisdn_is_sent()
    {
        $network                          = factory(Network::class)->make(['slug' => 'rede']);
        $pointOfSale                      = factory(PointOfSale::class)->make(['network' => $network]);
        $pointOfSale->providerIdentifiers = json_encode(["TIM" => 'ada']);
        $serviceTim                       = $this->timFactories()
            ->of(TimBRExpress::class)
            ->make(['msisdn' => 112381987238])
            ->toArray();
        $sale                             = factory(Sale::class)->make([
            'pointOfSale' => $pointOfSale->toArray(),
            'services'    => [$serviceTim]
        ]);
        $adapted                          = TimBRM4uRequestAdapter::adapt($sale->services[0]);
        self::assertEquals('MSISDN', $adapted['id']['type']);
    }

    /** @test */
    public function should_return_new_cust_code_when_pdv_has_tim_is_sent()
    {
        $network                          = factory(Network::class)->make(['slug' => 'rede']);
        $pointOfSale                      = factory(PointOfSale::class)->make(['network' => $network]);
        $pointOfSale->providerIdentifiers = json_encode(["TIM" => 'ada']);

        $serviceTim = $this->timFactories()
            ->of(TimBRExpress::class)
            ->make(['msisdn' => 112381987238])
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $pointOfSale->toArray(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBRM4uRequestAdapter::adapt($sale->services[0]);
        self::assertNotEmpty($adapted['pdv']['custcode']);
    }

    /** @test */
    public function should_return_contract_when_msisdn_is_sent()
    {
        $network                          = factory(Network::class)->make(['slug' => 'rede']);
        $pointOfSale                      = factory(PointOfSale::class)->make(['network' => $network]);
        $pointOfSale->providerIdentifiers = json_encode(["TIM" => 'ada']);
        $serviceTim                       = $this->timFactories()
            ->of(TimBRExpress::class)
            ->states('migration')
            ->make()
            ->toArray();
        $sale                             = factory(Sale::class)->make([
            'pointOfSale' => $pointOfSale->toArray(),
            'services'    => [$serviceTim]
        ]);
        $adapted                          = TimBRM4uRequestAdapter::adapt($sale->services[0]);
        self::assertEquals('MSISDN', $adapted['id']['type']);
        self::assertNotEmpty($adapted['id']['value']);
    }

    /** @test */
    public function should_return_contract_when_msisdn_and_iccid_is_sent()
    {
        $network                          = factory(Network::class)->make(['slug' => 'rede']);
        $pointOfSale                      = factory(PointOfSale::class)->make(['network' => $network]);
        $pointOfSale->providerIdentifiers = json_encode(["TIM" => 'ada']);
        $expectedIccid                    = '1128731982';
        $serviceTim                       = $this->timFactories()
            ->of(TimBRExpress::class)
            ->make(['iccid' => $expectedIccid])
            ->toArray();
        $sale                             = factory(Sale::class)->make([
            'pointOfSale' => $pointOfSale->toArray(),
            'services'    => [$serviceTim]
        ]);
        $adapted                          = TimBRM4uRequestAdapter::adapt($sale->services[0]);
        self::assertEquals('ICCID', $adapted['id']['type']);
        self::assertEquals($expectedIccid, $adapted['id']['value']);
    }
}
