<?php

namespace ClaroBR\Tests\Unit\Adapters;

use ClaroBR\Adapters\TradeAppToSivAdapter;
use ClaroBR\Models\ClaroPre;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Tests\TestCase;

class TradeAppToSivAdapterTest extends TestCase
{
    use SivFactoriesHelper;

    /** @test */
    public function shoul_not_return_promotion_when_chip_combo()
    {
        $serviceTim = $this->sivFactories()
            ->of(ClaroPre::class)
            ->states('chipCombo')
            ->make()
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => factory(PointOfSale::class)->make()->toArray(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TradeAppToSivAdapter::adapt($sale->services[0]);
        self::assertArrayHasKey('chip_combo', $adapted['service']);
        self::assertArrayNotHasKey('promocao_id', $adapted['service']);
    }

    /** @test */
    public function shoul_return_promotion_when_not_chip_combo()
    {
        $serviceTim = $this->sivFactories()
            ->of(ClaroPre::class)
            ->make()
            ->toArray();
        $sale       = factory(Sale::class)->make([
            'pointOfSale' => factory(PointOfSale::class)->make()->toArray(),
            'services'    => [$serviceTim]
        ]);
        $adapted    = TradeAppToSivAdapter::adapt($sale->services[0]);
        self::assertArrayNotHasKey('chip_combo', $adapted['service']);
        self::assertArrayHasKey('promocao_id', $adapted['service']);
    }
}
