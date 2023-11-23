<?php

use ClaroBR\Models\ClaroPos;
use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Enumerators\Modes;

$factory->define(ClaroPos::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CLARO_POS';
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'msisdn'      => 11555554444,
        'dueDate'     => 2,
        'invoiceType' => 'EMAIL',
        'mode'        => Modes::MIGRATION,
        'promotion'   => random_int(5, 8),
        'customer'    => [
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => now()->format('Y-m-d'),
            'filiation'        => 'teste',
            'mainPhone'        => 'teste',
            'secondaryPhone'   => 'teste',
            'salaryRange'      => 'teste',
            'profession'       => 'teste',
            'maritalStatus'    => 'teste',
            'rg'               => 'teste',
            'rgLocal'          => 'teste',
            'rgDate'           => 'teste',
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

$factory->state(ClaroPos::class, 'activation', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CLARO_POS';
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'dueDate'     => 2,
        'invoiceType' => 'EMAIL',
        'iccid'     => $faker->numerify('8955################'),
        'mode'        => Modes::ACTIVATION,
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

$factory->state(ClaroPos::class, 'without_customer', function () {
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CLARO_POS';
    return [
        'sector'    => $sector,
        'operator'  => $operator,
        'operation' => $operation,
        'promotion' => random_int(5, 8),
        'customer'  => [
            'email'            => 'teste',
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => 'teste',
            'gender'           => 'teste',
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
            'number'           => 'teste',
            'zipCode'          => 'teste',
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'local'            => 'teste',
            'localId'          => 'teste',
            'city'             => 'teste',
            'state'            => ''
        ]
    ];
});

$factory->state(ClaroPos::class, 'portability', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CLARO_POS';
    return [
        'sector'       => $sector,
        'operator'     => $operator,
        'operation'    => $operation,
        'portedNumber' => 1199999999,
        'msisdn'       => 1199999999,
        'dueDate'      => 2,
        'invoiceType'  => 'EMAIL',
        'mode'         => Modes::MIGRATION,
        'promotion'    => random_int(5, 8),
        'customer'     => [
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
            'number'           => 'teste',
            'zipCode'          => 'teste',
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'local'            => 'teste',
            'localId'          => 'teste',
            'city'             => 'teste',
            'state'            => 'SP'
        ],
        'status'       => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});

$factory->state(ClaroPos::class, 'dependent_with_iccid', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CONTROLE_BOLETO';
    return [
        'sector'       => $sector,
        'operator'     => $operator,
        'operation'    => $operation,
        'product'      => 'CB_2.5GB_ILIM_LOCAL',
        'promotion'    => 121221,
        'areaCode'     => '11',
        'iccid'        => $faker->numerify('8955################'),
        'msisdn'       => '11995555550',
        'portedNumber' => '11995555550',
        'invoiceType'  => 'VIA_POSTAL',
        'dependents'   => [
            [
                'mode'      => Modes::ACTIVATION,
                'product'   => 123,
                'type'      => \ClaroBR\Enumerators\ClaroBRDependents::CLARO_VOZ_DADOS,
                'promotion' => 13,
                'iccid'     => $faker->numerify('8955################'),
            ]
        ],
        'customer'     => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
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
            'number'           => 'teste',
            'zipCode'          => 'teste',
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'local'            => 'teste',
            'localId'          => 'teste',
            'city'             => 'teste',
            'state'            => 'SP'
        ],
        'status'       => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});

$factory->state(ClaroPos::class, 'dependent_portability', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CONTROLE_BOLETO';
    return [
        'sector'       => $sector,
        'operator'     => $operator,
        'operation'    => $operation,
        'product'      => 'CB_2.5GB_ILIM_LOCAL',
        'promotion'    => 121221,
        'areaCode'     => '11',
        'iccid'        => $faker->numerify('8955################'),
        'msisdn'       => '11995555550',
        'portedNumber' => '11995555550',
        'invoiceType'  => 'VIA_POSTAL',
        'dependents'   => [
            [
                'mode'         => Modes::PORTABILITY,
                'product'      => 123,
                'type'         => \ClaroBR\Enumerators\ClaroBRDependents::CLARO_VOZ_DADOS,
                'promotion'    => 13,
                'iccid'        => $faker->numerify('8955################'),
                'portedNumber' => '11995555550',
            ]
        ],
        'customer'     => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
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
            'number'           => 'teste',
            'zipCode'          => 'teste',
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'local'            => 'teste',
            'localId'          => 'teste',
            'city'             => 'teste',
            'state'            => 'SP'
        ],
        'status'       => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});
