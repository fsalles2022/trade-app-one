<?php

use Faker\Generator as Faker;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;

$factory->define(Sale::class, function (Faker $faker) {
    return [
        'user' => factory(User::class)->make()->toArray(),
        'pointOfSale' => factory(PointOfSale::class)->make()->toArray(),
        'saleTransaction' => (new Sale())->setTransactionNumber()['saleTransaction'],
        'services' => factory(Service::class)->make()->toArray()
    ];
});

$factory->state(Sale::class, 'two_services', function (Faker $faker) {
    return [
        'user' => factory(User::class)->make()->toArray(),
        'pointOfSale' => factory(PointOfSale::class)->make()->toArray(),
        'saleTransaction' => (new Sale())->setTransactionNumber(),
        'services' => factory(Service::class, 2)->make()->toArray()
    ];
});

$factory->state(Sale::class, 'four_services', function (Faker $faker) {
    return [
        'user' => factory(User::class)->make()->toArray(),
        'pointOfSale' => factory(PointOfSale::class)->make()->toArray(),
        'saleTransaction' => (new Sale())->setTransactionNumber(),
        'services' => factory(Service::class, 4)->make()->toArray()
    ];
});
