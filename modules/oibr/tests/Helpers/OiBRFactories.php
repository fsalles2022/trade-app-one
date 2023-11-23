<?php

namespace OiBR\Tests\Helpers;

use Illuminate\Database\Eloquent\Factory;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

trait OiBRFactories
{
    public function oiBRfactory(): Factory
    {
        return Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/oibr/Factories/')
        );
    }

    public function pointOfSaleOiBR()
    {
        $network = factory(Network::class)->create();
        return factory(PointOfSale::class)->create([
            'networkId'           => $network->id,
            'providerIdentifiers' => json_encode([
                Operations::OI => 'CCAS'
            ])
        ]);
    }
}
