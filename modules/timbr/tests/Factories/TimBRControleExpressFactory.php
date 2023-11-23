<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TimBR\Models\TimBRExpress;
use TradeAppOne\Domain\Enumerators\Modes;

$factory->define(TimBRExpress::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_EXPRESS;
    return [
        'sector'           => $sector,
        'operator'         => $operator,
        'operation'        => $operation,
        'areaCode'         => 11,
        'iccid'            => $faker->numerify('8955################'),
        'dueDate'          => 2,
        'invoiceType'      => 'EMAIL',
        'creditCard'       => [
            'token' => $faker->numerify('4242################'),
            'cvv'   => $faker->numerify('###'),
        ],
        'eligibilityToken' => $faker->numerify('2018####################'),
        'mode'             => Modes::ACTIVATION,
        'product'          => random_int(5, 8),
        'customer'         => [
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => $faker->date(),
            'filiation'        => 'teste',
            'mainPhone'        => 'teste',
            'secondaryPhone'   => 'teste',
            'salaryRange'      => 'teste',
            'profession'       => 'teste',
            'maritalStatus'    => 'teste',
            'rg'               => 'teste',
            'rgLocal'          => 'teste',
            'rgDate'           => $faker->date(),
            'rgState'          => 'teste',
            'number'           => $faker->numerify('###'),
            'zipCode'          => 'teste',
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'local'            => 'teste',
            'localId'          => 'teste',
            'city'             => 'teste',
            'state'            => 'SP',
            'state'            => 'SP',
            'country'          => 'Brasil'
        ],
        'status'           => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});

$factory->state(TimBRExpress::class, 'migration', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_EXPRESS;
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'msisdn'      => 1199999999,
        'dueDate'     => 2,
        'invoiceType' => 'EMAIL',
        'mode'        => Modes::MIGRATION,
        'areaCode'    => random_int(11, 14),
        'promotion'   => random_int(5, 8),
        'customer'    => [
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => 'teste',
            'filiation'        => 'teste',
            'mainPhone'        => 'teste',
            'secondaryPhone'   => 'teste',
            'salaryRange'      => 'teste',
            'profession'       => 'teste',
            'maritalStatus'    => 'teste',
            'rg'               => 'teste',
            'rgLocal'          => 'teste',
            'rgDate'           => 'teste',
            'rgState'          => 'teste',
            'number'           => 'teste',
            'zipCode'          => 'teste',
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'local'            => 'teste',
            'localId'          => 'teste',
            'city'             => 'teste',
            'state'            => 'SP'
        ],
        'status'      => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});
