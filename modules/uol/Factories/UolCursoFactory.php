<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use Uol\Models\UolCurso;

$factory->define(UolCurso::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));

    return [
        'operator' => Operations::UOL,
        'operation' => Operations\UolOperations::UOL_STANDARD,
        'product' => '1',
        'passportType' => '1',
        'mode' => Modes::ACTIVATION,
        'customer' => [
            "firstName" => $faker->firstName,
            "lastName" => $faker->lastName,
            "cpf" => $faker->unique()->cpf(false),
            "local" => "Alameda Rio Negro",
            "number" => "23",
            "complement" => "",
            "neighborhood" => $faker->address,
            "city" => "Barueri",
            "state" => "SP",
            "zipCode" => $faker->numerify("########"),
            "mainPhone" => $faker->e164PhoneNumber,
            "email" => $faker->safeEmail,
            "password" => $faker->randomNumber(8)
        ],
        'status' => \TradeAppOne\Domain\Enumerators\ServiceStatus::PENDING_SUBMISSION
    ];
});
