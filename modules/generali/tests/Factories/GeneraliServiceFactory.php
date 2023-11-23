<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use Generali\Models\Generali;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

$factory->define(Generali::class, static function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));

    return [
        'operator'  => Operations::GENERALI,
        'mode'      => Modes::ACTIVATION,
        'operation' => Operations::GENERALI_ELECTRONICS,
        'product' => [
            'reference' => '132',
            'slug'      => 'ge_tradeup',
            'label'     => 'GARANTIA ESTENDIDA ORIGINAL',
            "plan" => [
                "reference" => "204",
                "slug" => "ge_tradeup",
                'label' => 'GARANTIA ESTENDIDA ORIGINAL',
                "grossAmount" => 95.82,
                "validity" => 24,
                "code" => 20501008
            ]
        ],
        'device' =>  [
            "price" => 1200,
            "imei" => "000000000000110",
            "serialNumber" => null,
            "model" => "Gear S2 Classic",
            "date" => "2019-11-01T00:00:00-03:00",
            "id" => 5,
            "brand" => "Samsung",
            "color" => "Preto",
            "storage" => "Todas",
            "imageFront" => null,
            "imageBehind" => null,
            "createdAt" => "2019-01-15 16:47:07",
            "label" => "Samsung Gear S2 Classic Preto",
            "type" => "SMARTPHONE",
            "warrantyManufacturer" => 12,
        ],
        'customer' => [
            'email'     => $faker->unique()->safeEmail,
            'firstName' => $faker->firstName,
            'lastName'  => $faker->lastName,
            'gender'    => $faker->randomElement(['M','F']),
            'cpf'       => $faker->unique()->cpf(false),
            'rg'        => '181752979',
            'rgDate'    => '2000-10-28',
            'rgLocal'   => 'SSP',
            'rgState'   => 'SP',
            'birthday'  => '1995-06-19',
            'zipCode'   => $faker->numerify("########"),
            'mainPhone' => $faker->e164PhoneNumber,
            'secondaryPhone' => $faker->e164PhoneNumber,
            'number'         => '462',
            'neighborhood'   => $faker->address,
            'local'          => $faker->locale,
            'localId'        => 'ALAMEDA',
            'city'           => $faker->city,
            'state'          => 'SP',
        ],
        'payment' => [
            'gatewayReference'     => '5df2b40a96ecd',
            'gatewayTransactionId' => '25656057-A5D1-73A1-0CE9-3B75C61974B4',
            'gatewayStatus'        => 'AUTHORIZED',
            'date'                 => '2019-12-12T19:41:30-02:00',
            'times'                => 1,
            'interest'             => 5.76,
            'status'               => 'APPROVED',
        ],
        'sector' => 'INSURERS',
        'status' => ServiceStatus::SUBMITTED,
        "premium" => [
            "total" => 95.82,
            "price" => 95.82,
            "validity" =>  [
                "start" => "2020-12-31",
                "end" => "2022-12-31",
            ]
        ],
        'price'     => 1269.54,
        'serviceTransaction' => '201912121941036750-0',
        'card' => [
            'token' => '4370f54683c461a182d9914d9d7581bb0308cbf404b8bf473d298febd1bb4d96',
            'date'  => '2019-12-12T21:41:30.608Z',
        ],
        'policyId' => '719530200000001'
    ];
});
