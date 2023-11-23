<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use Gateway\Models\Customer;

$factory->define(Customer::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    return [
        'customerIdentity' => '1',
        'cpf' => $faker->unique()->cpf(false),
        'name' => $faker->firstName,
        'email' => $faker->safeEmail
    ];
});
