<?php

use ClaroBR\Models\ClaroPre;
use Faker\Generator as Faker;
use Faker\Provider\pt_BR;

$factory->define(ClaroPre::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CLARO_PRE';
    return [
        'sector'              => $sector,
        'operator'            => $operator,
        'operation'           => $operation,
        'promotion'           => ['product' => rand(2, 912)],
        'product'             => rand(2, 912),
        'iccid'               => $faker->numerify('8955################'),
        'operatorIdentifiers' => ['servico_id' => '12', 'venda_id' => '123'],
        'customer'            => [
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => $faker->unique()->cpf(false),
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
            'state'            => 'SP'
        ],
        'status'              => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});


$factory->state(ClaroPre::class, 'chipCombo', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CLARO_PRE';
    return [
        'sector'              => $sector,
        'operator'            => $operator,
        'operation'           => $operation,
        'promotion'           => rand(2, 9),
        'product'             => rand(2, 912),
        'promotion'           => ['product' => rand(2, 912)],
        'chipCombo'           => true,
        'iccid'               => $faker->numerify('8955################'),
        'operatorIdentifiers' => ['servico_id' => '12', 'venda_id' => '123'],
        'customer'            => [
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => $faker->unique()->cpf(false),
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
            'state'            => 'SP'
        ],
        'status'              => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});
