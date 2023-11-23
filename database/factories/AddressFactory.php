<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Models\Collections\Address;

$factory->define(Address::class, function (Faker $faker) {

    $faker->addProvider(new pt_BR\Address($faker));

    return [
        'zipCode' => $faker->postcode,
        'local' => $faker->streetName,
        'neighborhood' => $faker->citySuffix,
        'state' => $faker->stateAbbr,
        'number' => $faker->buildingNumber,
        'city' => $faker->city,
        'complement' => $faker->secondaryAddress,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude
    ];
});
