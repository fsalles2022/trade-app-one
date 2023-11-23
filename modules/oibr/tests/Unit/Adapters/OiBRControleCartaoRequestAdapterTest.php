<?php

namespace OiBR\Tests\Unit\Adapters;

use OiBR\Adapters\OiBRControleCartaoRequestAdapter;
use OiBR\Models\OiBRControleCartao;
use OiBR\OiBRIdentifierNotFound;
use OiBR\Tests\Helpers\OiBRFactories;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Tests\TestCase;

class OiBRControleCartaoRequestAdapterTest extends TestCase
{
    use OiBRFactories;

    /** @test */
    public function should_throw_identifier_exception()
    {
        $pointOfSale = factory(PointOfSale::class)->make(['operatorIdentifiers' => null]);
        $service     = $this->oiBRfactory()->of(OiBRControleCartao::class)->make()->toArray();
        $sale        = factory(Sale::class)->make(['services' => [$service], 'pointOfSale' => $pointOfSale]);
        $this->expectException(OiBRIdentifierNotFound::class);
        $returned = OiBRControleCartaoRequestAdapter::adapt($sale->services[0]);
    }

    /** @test */
    public function should_map_user_fields()
    {
        $pointOfSale = $this->pointOfSaleOiBR()->toArray();
        $service     = $this->oiBRfactory()->of(OiBRControleCartao::class)->make()->toArray();
        $sale        = factory(Sale::class)->make(['services' => [$service], 'pointOfSale' => $pointOfSale]);
        $returned    = OiBRControleCartaoRequestAdapter::adapt($sale->services[0]);
        self::assertArrayHasKey('comissionamento', $returned);
        self::assertArrayHasKey('estabelecimento', $returned['comissionamento']);
    }
}
