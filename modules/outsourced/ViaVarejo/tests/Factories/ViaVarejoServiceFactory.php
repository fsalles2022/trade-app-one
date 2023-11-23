<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use Outsourced\ViaVarejo\Models\ViaVarejo;

$factory->define(ViaVarejo::class, static function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));

    return [
        "operator" => "CLARO",
        "operation" => "CONTROLE_BOLETO",
        "product" => 251,
        "dueDate" => 21,
        "iccid" => "89550895464654654654",
        "areaCode" => "71",
        "invoiceType" => "EMAIL",
        "promotion" => [
            "label" => "Controle Super 3GB - S/Fidel - 88075",
            "price" => -5.0,
            "product" => 524,
        ],
        "mode" => "ACTIVATION",
        "imei" => "459743559552212",
        "device" => [
            "id" => 4959,
            "sku" => "459743559552212",
            "model" => "LG G2 LITE DUAL-BRANCO",
            "label" => "LG G2 LITE DUAL",
            "brand" => "LG",
            "color" => "BRANCO",
            "storage" => "4GB",
            "networkId" => 43,
            "createdAt" => "2019-10-02 07:27:19",
            "updatedAt" => "2020-08-27 20:14:50",
            "priceWithout" => 599.0,
            "priceWith" => 44.0,
            "discount" => 555.0,
        ],
        "discount" => [
            "id" => 527,
            "title" => "Via Mais",
            "discount" => 555.0,
            "price" => null,
            "promotion" => "524",
            "idCampaign" => 1,
            "coupon" => "AM3684CLARO3GB"
        ],
        "customer" => [
            "cpf" => $faker->unique()->cpf(false),
            "firstName" => $faker->firstName,
            "lastName" => $faker->lastName,
            "email" => $faker->unique()->safeEmail,
            "gender" => $faker->randomElement(['M','F']),
            "birthday" => "1980-09-05",
            "filiation" => "Maria Cesar",
            "mainPhone" => "+5511978797897",
            "secondaryPhone" => "+5511987987979",
            "rg" => "54464656444",
            "rgLocal" => "SSP",
            "rgState" => "SP",
            "zipCode" => $faker->numerify("########"),
            "localId" => 56,
            "local" => $faker->locale,
            "state" => "SP",
            "city" => $faker->city,
            "neighborhood" => $faker->address,
            "number" => $faker->randomNumber(4),
            "complement" => null
        ],
        "sector" => "TELECOMMUNICATION",
        "status" => 'PENDING_SUBMISSION',
        "label" => "Controle Super 3GB",
        "price" => 49.99,
        "serviceTransaction" => "202010291300282937-0",
        "_id" => "5f9ae71d60065711e143b43c",
        "operatorIdentifiers" => [
            "venda_id" => 9173647,
            "servico_id" => 8874832
        ],
        "updatedAt" => "2020-10-29 17:09:02",
    ];
});
