<?php

namespace VivoBR\Tests\Helpers;

use Illuminate\Database\Eloquent\Factory;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

trait VivoFactoriesHelper
{
    public function sunFactories(): Factory
    {
        return Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/vivobr/Factories/')
        );
    }

    public function saleFactory(array $services): Sale
    {
        $network     = factory(Network::class)->make(['slug' => 'cea'])->toArray();
        $pointOfSale = factory(PointOfSale::class)->make(['network' => $network])->toArray();
        return \factory(Sale::class)->make(['services' => $services, 'pointOfSale' => $pointOfSale]);
    }
}
