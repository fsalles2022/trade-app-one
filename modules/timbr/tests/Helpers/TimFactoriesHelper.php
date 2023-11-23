<?php

namespace TimBR\Tests\Helpers;

use Illuminate\Database\Eloquent\Factory;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

trait TimFactoriesHelper
{
    public function timFactories(): Factory
    {
        return Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/timbr/tests/Factories')
        );
    }

    public function getPointOfSaleWithTimIdentifiers(): PointOfSale
    {
        $network     = factory(Network::class)->make(['slug' => 'rede']);
        $pointOfSale = factory(PointOfSale::class)->make();

        $pointOfSale->providerIdentifiers = json_encode(['TIM' => 'ada']);

        $pointOfSale->setRelation('network', $network);

        return $pointOfSale;
    }
}
