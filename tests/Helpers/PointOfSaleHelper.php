<?php


namespace TradeAppOne\Tests\Helpers;

use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

trait PointOfSaleHelper
{
    public function withNetwork()
    {
        $network     = factory(Network::class)->create();
        $pointOfSale = factory(PointOfSale::class)->make();
        $pointOfSale->network()->associate($network)->save();
        return $pointOfSale;
    }

    public function sameNetwork(int $quantity = 1)
    {
        $network      = factory(Network::class)->create();
        $pointOfSales = factory(PointOfSale::class, $quantity)->make();
        $network->pointsOfSale()->saveMany($pointOfSales);

        return $pointOfSales;
    }

    public function sameUserNetwork($network)
    {
        $pointOfSale = factory(PointOfSale::class)->make();
        $pointOfSale->network()->associate($network)->save();

        return $pointOfSale;
    }
}
