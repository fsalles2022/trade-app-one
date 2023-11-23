<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use VivoBR\Enumerators\VivoInvoiceType;
use VivoBR\Models\VivoInternetMovelPos;

$factory->define(VivoInternetMovelPos::class, function (Faker $faker) {

    $faker->addProvider(new pt_BR\Person($faker));

    return [
        "operator" => Operations::VIVO,
        "operation" => Operations::VIVO_INTERNET_MOVEL_POS,
        "product" => "1696",
        "mode" => Modes::ACTIVATION,
        "dueDate" => 17,
        "iccid" => $faker->numerify("8955################"),
        "areaCode" => "11",
        "invoiceType" => $faker->randomElement([VivoInvoiceType::VIA_POSTAL, VivoInvoiceType::EMAIL]),
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

$factory->state(VivoInternetMovelPos::class, 'invalid_product', ['product' => '0000']);
