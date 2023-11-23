<?php

use Faker\Generator as Faker;
use Gateway\Models\CreditCard;

$factory->define(CreditCard::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\Payment($faker));
    return [
        'flag' => $faker->randomElement(['visa', 'mastercard', 'elo']),
        'cardHolder' => $faker->firstName,
        'cardNumber' => $faker->numerify('################'),
        'cardSecurityCode' => $faker->numerify('###'),
        'cardExpirationDate' => $faker->creditCardExpirationDateString
    ];
});
