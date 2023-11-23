<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use OiBR\Models\OiBRControleBoleto;
use OiBR\Models\OiBRControleCartao;
use TradeAppOne\Domain\Enumerators\Modes;

$factory->define(OiBRControleCartao::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = \TradeAppOne\Domain\Enumerators\Operations::TELECOMMUNICATION;
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::OI;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::OI_CONTROLE_CARTAO;
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'dueDate'     => 2,
        'invoiceType' => 'boleto_bancario',
        'mode'        => Modes::ACTIVATION,
        'iccid'       => $faker->numerify('8955################'),
        'areaCode'    => $faker->numerify('##'),
        'customer'    => [
            'score'            => 'I',
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => 'teste',
            'filiation'        => 'teste',
            'mainPhone'        => 'teste',
            'rg'               => 'teste',
            'number'           => $faker->numerify(3),
            'zipCode'          => 'teste',
            'neighborhood'     => $faker->address,
            'neighborhoodType' => 'teste',
            'city'             => 'teste',
            'state'            => 'SP'
        ],
        'status'      => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});

$factory->state(OiBRControleCartao::class, 'migration', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = \TradeAppOne\Domain\Enumerators\Operations::TELECOMMUNICATION;
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::OI;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::OI_CONTROLE_CARTAO;
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'dueDate'     => 2,
        'invoiceType' => 'boleto_bancario',
        'mode'        => Modes::MIGRATION,
        'msisdn'      => $faker->numerify('559########'),
        'promotion'   => random_int(5, 8),
        'customer'    => [
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => 'teste',
            'filiation'        => 'teste',
            'mainPhone'        => $faker->numerify('559########'),
            'rg'               => 'teste',
            'number'           => $faker->numerify(3),
            'zipCode'          => 'teste',
            'neighborhood'     => $faker->streetSuffix,
            'local'            => $faker->streetName,
            'neighborhoodType' => 'teste',
            'city'             => 'teste',
            'state'            => 'SP'
        ],
        'status'      => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});
