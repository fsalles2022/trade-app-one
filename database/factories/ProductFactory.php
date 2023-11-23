<?php

use Faker\Generator as Faker;
use FastShop\Models\Product;
use TradeAppOne\Domain\Models\Tables\Service;

$factory->define(Product::class, static function (Faker $faker) {
    $extras = new StdClass();
    $extras->plan = $faker->word;
    $extras->minutes = $faker->sentence;

    $original = new StdClass();
    $original->plan = $faker->word;
    $original->minutes = $faker->sentence;
    $original->price = $faker->randomFloat(2);

    return [
        'code'       => $faker->word,
        'title'      => $faker->sentence(3, true),
        'areaCode'   => $faker->randomNumber(1),
        'loyaltyMonths' => $faker->randomNumber(1),
        'price'      => $faker->randomFloat(2),
        'internet'   => $faker->randomNumber(1),
        'minutes'    => $faker->randomNumber(1),
        'serviceId'  => Service::first()->id,
        'extras'     => json_encode($extras),
        'original'   => json_encode($original)
    ];
});