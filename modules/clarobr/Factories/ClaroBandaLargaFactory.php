<?php

use ClaroBR\Models\ClaroBandaLarga;
use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Enumerators\Formats;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

$factory->define(ClaroBandaLarga::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));

    $birthdayBeetwen30and20Years     = $faker
        ->dateTimeBetween($startDate = '-30 years', $endDate = '-20 years')
        ->format(Formats::DATE);

    return [
        'sector'      => Operations::TELECOMMUNICATION,
        'operator'    => Operations::CLARO,
        'operation'   => Operations::CLARO_BANDA_LARGA,
        'dueDate'     => 17,
        'iccid'       => $faker->numerify('8955################'),
        'invoiceType' => 'VIA_POSTAL',
        'mode'        => Modes::ACTIVATION,
        'status'      => ServiceStatus::SUBMITTED,
        'promotion'   => random_int(5, 8),
        'areaCode'    => $faker->areaCode,
        'customer'    => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => $birthdayBeetwen30and20Years,
            'filiation'        => 'maria lucia marques de almeida prado',
            'mainPhone'        => '+5511999999999',
            'secondaryPhone'   => '+5514999999999',
            'salaryRange'      => '3',
            'profession'       => '5',
            'maritalStatus'    => '2',
            'rg'               => '216416232323',
            'rgLocal'          => 'sspsp',
            'rgDate'           => '2010-02-11',
            'number'           => '323',
            'zipCode'          => '06160100',
            'neighborhood'     => 'Pinheiros',
            'local'            => 'Rua Gen Hasegawa',
            'localId'          => '110',
            'city'             => 'São Paulo',
            'state'            => 'SP'
        ]
    ];
});
