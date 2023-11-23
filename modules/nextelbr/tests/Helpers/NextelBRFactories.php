<?php

namespace NextelBR\Tests\Helpers;

use Illuminate\Database\Eloquent\Factory;
use NextelBR\Enumerators\NextelBRConstants;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

trait NextelBRFactories
{
    public function factory(): Factory
    {
        return Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/nextelbr/Factories/')
        );
    }

    public function pointOfSaleNextel()
    {
        $network = factory(Network::class)->create();
        return factory(PointOfSale::class)->create([
            'networkId'           => $network->id,
            'providerIdentifiers' => json_encode([
                Operations::NEXTEL
                => [
                    NextelBRConstants::POINT_OF_SALE_REF => 11,
                    NextelBRConstants::POINT_OF_SALE_COD => 11
                ]
            ])
        ]);
    }
}
