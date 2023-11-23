<?php

use ClaroBR\Models\ControleBoleto;
use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Enumerators\Modes;

$factory->define(ControleBoleto::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CONTROLE_BOLETO';
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'dueDate'     => 1,
        'iccid'       => $faker->numerify('8955################'),
        'invoiceType' => 'VIA_POSTAL',
        'msisdn'      => '11995555550',
        'mode'        => Modes::MIGRATION,
        'product'     => '32',
        'promotion'   => random_int(5, 8),
        'areaCode'    => $faker->areaCode,
        'customer'    => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => $faker->dateTimeBetween()->format(\TradeAppOne\Domain\Enumerators\Formats::DATE),
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

$factory->state(ControleBoleto::class, 'activation_without_portability', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CONTROLE_BOLETO';
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'promotion'   => 121221,
        'product'     => 'CB_2.5GB_ILIM_LOCAL',
        'dueDate'     => 1,
        'iccid'       => $faker->numerify('8955################'),
        'invoiceType' => 'VIA_POSTAL',
        'customer'    => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => $faker->dateTimeBetween()->format(\TradeAppOne\Domain\Enumerators\Formats::DATE),
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

$factory->state(ControleBoleto::class, 'activation_with_portability', function (Faker $faker) {
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
        'dueDate'      => 1,
        'iccid'        => $faker->numerify('8955################'),
        'portedNumber' => '11995555550',
        'invoiceType'  => 'VIA_POSTAL',
        'customer'     => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => $faker->dateTimeBetween()->format(\TradeAppOne\Domain\Enumerators\Formats::DATE),
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

$factory->state(ControleBoleto::class, 'migration_with_chip', function (Faker $faker) {
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
        'customer'     => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => $faker->dateTimeBetween()->format(\TradeAppOne\Domain\Enumerators\Formats::DATE),
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

$factory->state(ControleBoleto::class, 'migration_without_chip', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $faker->addProvider(new pt_BR\PhoneNumber($faker));

    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CONTROLE_BOLETO';
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'product'     => 'CB_2.5GB_ILIM_LOCAL',
        'promotion'   => 121221,
        'areaCode'    => $faker->areaCode,
        'msisdn'      => '11995555550',
        'invoiceType' => 'VIA_POSTAL',
        'customer'    => [
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
        'status'      => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});
