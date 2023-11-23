<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TimBR\Models\TimBRPrePago;
use TradeAppOne\Domain\Enumerators\Modes;

$factory->define(TimBRPrePago::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_PRE_PAGO;
    return [
        'sector'              => $sector,
        'operator'            => $operator,
        'operation'           => $operation,
        'areaCode'            => 11,
        'iccid'               => $faker->numerify('8955################'),
        'mode'                => Modes::ACTIVATION,
        'product'             => 'PL00001',
        'operatorIdentifiers' => [
            'protocol' => "231872389"
        ],
        'customer'            => [
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => \TimBR\Tests\TimBRTestBook::SUCCESS_CUSTOMER,
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
            'rgDate'           => 'teste',
            'rgState'          => 'teste',
            'number'           => 'teste',
            'zipCode'          => 'teste',
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'local'            => 'teste',
            'localId'          => 'teste',
            'city'             => 'teste',
            'state'            => 'SP',
            'zipCode'          => '06454000',
            'country'          => 'Brasil'
        ],
        'status'              => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});


$factory->state(TimBRPrePago::class, 'portability', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_PRE_PAGO;
    return [
        'sector'       => $sector,
        'operator'     => $operator,
        'operation'    => $operation,
        'product'      => 'PL00001',
        'portedNumber' => 1199999999,
        'iccid'        => $faker->numerify('8955################'),
        'mode'         => Modes::PORTABILITY,
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
        'status'       => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});
