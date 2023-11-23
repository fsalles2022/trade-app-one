<?php

use Faker\Generator as Faker;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Service;

$factory->define(AvailableService::class, static function(Faker $faker){

    $network = factory(Network::class)->create();

    return [
        'serviceId'     => factory(Service::class)->create()->id,
        'pointOfSaleId' => factory(PointOfSale::class)->create(['networkId' => $network->id])->id,
        'networkId'     => $network->id,
    ];
});
