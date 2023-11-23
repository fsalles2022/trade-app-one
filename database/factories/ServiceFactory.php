<?php

use Faker\Generator as Faker;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */

$factory->define(Service::class, function (Faker $faker): array {
    $serviceRandom = array_rand(Operations::SECTORS);
    $operator = array_rand(Operations::SECTORS[$serviceRandom]);
    $operation = array_rand(Operations::SECTORS[$serviceRandom][$operator]);
    return [
        'price' => 278,
        'sector' => $serviceRandom,
        'operator' => $operator,
        'operation' => $operation,
        'customer' => [
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
        ]
    ];
});

$factory->state(Service::class, 'updateImei', function (Faker $faker): array {
    $serviceRandom = array_rand(Operations::SECTORS);
    $operator = array_rand(Operations::SECTORS[$serviceRandom]);
    $operation = array_rand(Operations::SECTORS[$serviceRandom][$operator]);

    return [
        'price' => 150,
        'sector' => $serviceRandom,
        'operator' => $operator,
        'operation' => $operation,
        'imei' => '234234234234',
        'customer' => [
            'firstName' => $faker->firstName,
            'lastName' => $faker->lastName,
            'cpf' => '79955495804'
        ]
    ];
});
