<?php

namespace OiBR\Tests\Unit\Adapters;

use OiBR\Adapters\OiBRRequestAdapter;
use OiBR\Models\OiBRControleBoleto;
use OiBR\OiBRIdentifierNotFound;
use OiBR\Tests\Helpers\OiBRFactories;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Tests\TestCase;

class OiBRRequestAdapterTest extends TestCase
{
    use OiBRFactories;

    /** @test */
    public function should_return_contact_number_whitou_country()
    {
        $controleBoleto      = $this->oiBRfactory()
            ->of(OiBRControleBoleto::class)
            ->states('migration')
            ->make()
            ->toArray();
        $pointOfSaleWithOiBR = $this->pointOfSaleOiBR();
        $sale                = factory(Sale::class)
            ->make([
                'pointOfSale' => $pointOfSaleWithOiBR,
                'services'    => [$controleBoleto]
            ]);
        $adapter             = new OiBRRequestAdapter();
        $result              = $adapter->adapt($sale->services->first());
        self::assertEquals(11, strlen($result['numeroContato']));
    }

    /** @test */
    public function should_return_exception_when_oiBR_id_not_found()
    {
        $this->expectException(OiBRIdentifierNotFound::class);
        $controleBoleto = $this->oiBRfactory()
            ->of(OiBRControleBoleto::class)
            ->states('migration')
            ->make()
            ->toArray();
        $sale           = factory(Sale::class)->make(['services' => [$controleBoleto]]);
        $adapter        = new OiBRRequestAdapter();
        $result         = $adapter->adapt($sale->services->first());
        self::assertEquals(11, strlen($result['numeroContato']));
    }


    /** @test */
    public function should_return_tipoVendedor_when_migration()
    {
        $controleBoleto      = $this->oiBRfactory()
            ->of(OiBRControleBoleto::class)
            ->states('migration')
            ->make()
            ->toArray();
        $pointOfSaleWithOiBR = $this->pointOfSaleOiBR();
        $sale                = factory(Sale::class)
            ->make([
                'pointOfSale' => $pointOfSaleWithOiBR,
                'services'    => [$controleBoleto]
            ]);
        $adapter             = new OiBRRequestAdapter();
        $result              = $adapter->adapt($sale->services->first());
        self::assertNotEmpty($result['tipoVendedor']);
        self::assertEquals('CPF', $result['tipoVendedor']);
    }
}
