<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use SurfPernambucanas\Models\SurfPernambucanasPrePago;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

$factory->define(SurfPernambucanasPrePago::class, function (Faker $faker): array {
    $faker->addProvider(new pt_BR\Person($faker));

    return [
        "operator" => Operations::SURF_PERNAMBUCANAS,
        "operation" => Operations::SURF_PERNAMBUCANAS_PRE,
        "product" => '463',
        "mode" => Modes::ACTIVATION,
        "dueDate" => 17,
        "iccid" => $faker->numerify("8955################"),
        "areaCode" => "11",
        "invoiceType" => 'CARTAO_CREDITO',
        "msisdn" => '5511999998888',
        "customer" => [
            "cpf" => $faker->unique()->cpf(false),
            "firstName" => $faker->firstName,
            "lastName" => $faker->lastName,
            "birthday" => $faker->date(),
            "email" => $faker->safeEmail,
            "mainPhone" => $faker->e164PhoneNumber,
            "secondaryPhone" => $faker->e164PhoneNumber,
            "gender" => $faker->randomElement(['M', 'F']),
            "filiation" => $faker->name,
            "zipCode" => $faker->numerify("########"),
            "state" => "SP",
            "city" => "Barueri",
            "neighborhood" => $faker->address,
            "local" => "Alameda Rio Negro",
            "number" => "23",
            "complement" => "",
        ],
        "sector" => Operations::TELECOMMUNICATION,
        "status" => ServiceStatus::PENDING_SUBMISSION
    ];
});
