<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Models\Tables\UserAuthAlternates;

$factory->define(UserAuthAlternates::class, static function (Faker $faker) {

    $faker->addProvider(new pt_BR\Person($faker));
    $faker->addProvider(new pt_BR\PhoneNumber($faker));

    return [
        'userId' => $faker->randomNumber(1),
        'document'  => $faker->unique()->randomNumber(5),
    ];
});