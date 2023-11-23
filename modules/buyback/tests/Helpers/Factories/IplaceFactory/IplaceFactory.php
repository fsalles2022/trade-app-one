<?php

use Buyback\Models\Operations\Iplace;
use Faker\Generator;
use Faker\Generator as Faker;
use Faker\Provider\PhoneNumber;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\ServiceTransactionGenerator;

$factory->define(Iplace::class, function (Faker $faker) {
    $faker->addProvider(new \Buyback\Providers\Faker\Smartphone($faker));

    $sector    = Operations::TRADE_IN;
    $operator  = Operations::TRADE_IN_MOBILE;
    $operation = Operations::IPLACE;
    $imei      = (new PhoneNumber((new Generator())))->imei();

    $model     = $faker->model;
    $colorName = $faker->colorName;
    $storage   = $faker->randomElement(["32GB", "64GB", "128GB", "256GB"]);

    return [
        'serviceTransaction' => ServiceTransactionGenerator::generate(),
        'sector'             => $sector,
        'operator'           => $operator,
        'price'              => 0,
        'operation'          => $operation,
        'mode'               => Modes::ACTIVATION,
        'status'             => \TradeAppOne\Domain\Enumerators\ServiceStatus::ACCEPTED,
        'device'             => [
            'id'      => 1,
            'imei'    => $imei,
            'model'   => $model,
            'brand'   => $faker->brand,
            'color'   => $colorName,
            'label'   => "$model $colorName $storage",
            'storage' => $storage
        ],
        'evaluations'        => [
            'salesman' => [
                'price'      => '2',
                'deviceNote' => '2',
                'questions'  => [
                    [
                        'id'       => '1',
                        'question' => 'Teste',
                        'weight'   => 'Teste',
                        'answer'   => 'Teste',
                        'blocker'  => 'Teste',
                    ]
                ],
            ]
        ],
        'customer'           => [
            'email'          => $faker->email,
            'firstName'      => $faker->firstName,
            'lastName'       => $faker->lastName,
            'cpf'            => '24781956076',
            'gender'         => $faker->randomElement(['M', 'F']),
            'birthday'       => $faker->dateTimeBetween()->format(\TradeAppOne\Domain\Enumerators\Formats::DATE),
            'filiation'      => 'Joana Jesus',
            'mainPhone'      => '+5511956226555',
            'secondaryPhone' => '+5511956235586',
            'salaryRange'    => 1,
            'profession'     => 1,
            'maritalStatus'  => 1,
            'zipCode'        => '08051380',
            'localId'        => 110,
            'local'          => 'Rua João Tavares',
            'state'          => 'SP',
            'city'           => 'São Paulo',
            'neighborhood'   => 'Limoeiro',
            'number'         => '1254',
            'complement'     => 'Apartamento 23, Bloco 23',
            'rg'             => '001494951',
            'rgDate'         => '2003-03-28',
            'rgState'        => 'SP',
            'rgLocal'        => 'SSP',
        ],
    ];
});
