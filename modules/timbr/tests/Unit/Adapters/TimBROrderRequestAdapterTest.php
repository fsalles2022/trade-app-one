<?php

namespace TimBR\Tests\Unit\Adapters;

use TimBR\Adapters\TimBROrderRequestAdapter;
use TimBR\Enumerators\ResolvingTimOfferMistake;
use TimBR\Models\TimBRControleFatura;
use TimBR\Models\TimBRPrePago;
use TimBR\Tests\Helpers\TimFactoriesHelper;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Tests\TestCase;

class TimBROrderRequestAdapterTest extends TestCase
{
    use TimFactoriesHelper;

    /** @test */
    public function should_return_new_contract_when_iccid_is_sent()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->make()
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('newContract', $order);
        self::assertArrayHasKey('ddd', $order['newContract']);
        self::assertArrayHasKey('simCard', $order['newContract']);
        self::assertArrayHasKey('contract', $order);
        self::assertArrayNotHasKey('portability', $order);
    }

    /** @test */
    public function should_return_new_contract_in_v3_when_iccid_is_sent()
    {
        $serviceTim = $this->timFactories()
        ->of(TimBRControleFatura::class)
        ->make()
        ->toArray();
        $sale       = factory(Sale::class)->make([
        'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
        'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('id', $order['newContract']['simCard']);
        self::assertArrayHasKey('ddd', $order['newContract']);
        self::assertArrayHasKey('simCard', $order['newContract']);
        self::assertArrayHasKey('contract', $order);
        self::assertArrayNotHasKey('portability', $order);
    }

    /** @test */
    public function should_return_new_contract_when_iccid_and_msisdn_is_sent()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->make(['msisdn' => 112381987238])
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('newContract', $order);
        self::assertArrayHasKey('ddd', $order['newContract']);
        self::assertArrayHasKey('simCard', $order['newContract']);
        self::assertArrayHasKey('contract', $order);
        self::assertArrayNotHasKey('portability', $order);
    }

    /** @test */
    public function should_return_contract_when_msisdn_is_sent()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('migration')
            ->make()
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('contract', $order);
        self::assertArrayHasKey('msisdn', $order['contract']);
        self::assertArrayNotHasKey('portability', $order);
        self::assertArrayNotHasKey('newContract', $order);
        self::assertArrayNotHasKey('offers', $order);
    }

    /** @test */
    public function should_return_contract_when_msisdn_and_iccid_is_sent()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('migration')
            ->make(['iccid' => 1128731982])
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('contract', $order);
        self::assertArrayHasKey('msisdn', $order['contract']);
        self::assertArrayNotHasKey('portability', $order);
        self::assertArrayNotHasKey('newContract', $order);
        self::assertArrayNotHasKey('offers', $order);
    }

    /** @test */
    public function should_return_portability_and_new_contract_when_ported_number_is_sent()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('portability')
            ->make()
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('portability', $order);
        self::assertArrayHasKey('msisdn', $order['portability']);
        self::assertArrayHasKey('newContract', $order);
        self::assertArrayHasKey('simCard', $order['newContract']);
        self::assertArrayHasKey('ddd', $order['newContract']);
        self::assertArrayHasKey('contract', $order);
        self::assertArrayNotHasKey('offers', $order);
        self::assertArrayNotHasKey('offers', $order);
    }

    /** @test */
    public function should_return_portability_when_ported_number_and_msisdn_is_sent()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('portability')
            ->make(['msisdn' => 1128731982])
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('portability', $order);
        self::assertArrayHasKey('msisdn', $order['portability']);
        self::assertArrayHasKey('simCard', $order['newContract']);
        self::assertArrayHasKey('ddd', $order['newContract']);
        self::assertArrayHasKey('newContract', $order);
        self::assertArrayHasKey('contract', $order);
        self::assertArrayNotHasKey('offers', $order);
    }

    /** @test */
    public function should_return_portability_with_sim_card_when_ported_number_and_msisdn_is_sent()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('portability')
            ->make(['msisdn' => 1128731982])
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('portability', $order);
        self::assertArrayHasKey('msisdn', $order['portability']);
        self::assertArrayHasKey('simCard', $order['newContract']);
        self::assertArrayHasKey('newContract', $order);
        self::assertArrayHasKey('contract', $order);
        self::assertArrayNotHasKey('offers', $order);
    }

    /** @test */
    public function should_return_without_ddd_when_ported_number_and_msisdn_is_sent()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('portability')
            ->make(['msisdn' => 1128731982])
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('portability', $order);
        self::assertArrayHasKey('ddd', $order['newContract']);
        self::assertArrayHasKey('newContract', $order);
        self::assertArrayHasKey('contract', $order);
        self::assertArrayNotHasKey('offers', $order);
    }

    /** @test */
    public function should_return_portability_when_ported_number_and_iccid_is_sent()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('portability')
            ->make(['iccid' => 1128731982])
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('portability', $order);
        self::assertArrayHasKey('msisdn', $order['portability']);
        self::assertArrayHasKey('simCard', $order['newContract']);
        self::assertArrayHasKey('newContract', $order);
        self::assertArrayHasKey('contract', $order);
        self::assertArrayNotHasKey('offers', $order);
    }

    /** @test */
    public function should_return_portability_when_ported_number_and_msisdn_and_iccid_is_sent()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('portability')
            ->make(['msisdn' => 1128731982, 'iccid' => 1128731982])
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('portability', $order);
        self::assertArrayHasKey('msisdn', $order['portability']);
        self::assertArrayHasKey('newContract', $order);
        self::assertArrayHasKey('simCard', $order['newContract']);
        self::assertArrayHasKey('contract', $order);
        self::assertArrayNotHasKey('offers', $order);
    }

    /** @test */
    public function should_return_direct_debit()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('debito_automatico')
            ->make(['msisdn' => 1128731982, 'iccid' => 1128731982])
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('directDebit', $order['billingProfile']);
        self::assertArrayNotHasKey('offers', $order);
    }


    /** @test */
    public function should_return_attributes_direct_debit()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->states('debito_automatico')
            ->make(['msisdn' => 1128731982, 'iccid' => 1128731982])
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('bankCode', $order['billingProfile']['directDebit']);
        self::assertArrayHasKey('accountNumber', $order['billingProfile']['directDebit']);
        self::assertArrayHasKey('agencyCode', $order['billingProfile']['directDebit']);
        self::assertArrayNotHasKey('offers', $order);
    }

    /** @test */
    public function should_return_pre_without_invoice()
    {
        $serviceTim = $this->timFactories()
            ->of(TimBRPrePago::class)
            ->make()
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TimBROrderRequestAdapter::adapt($sale->services[0]);
        $order      = $adapted['order'];
        self::assertArrayHasKey('newContract', $order);
        self::assertArrayHasKey('ddd', $order['newContract']);
        self::assertArrayHasKey('simCard', $order['newContract']);
        self::assertEquals('PR00460', $order['offers'][0]['id']);
        self::assertArrayHasKey('contract', $order);
        self::assertArrayNotHasKey('portability', $order);
    }
}
