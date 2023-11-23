<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use NextelBR\Models\NextelBRControleBoleto;
use TradeAppOne\Domain\Enumerators\Modes;

$factory->define(NextelBRControleBoleto::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::NEXTEL;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::NEXTEL_CONTROLE_CARTAO;
    return [
        'sector'              => $sector,
        'operator'            => $operator,
        'operation'           => $operation,
        'dueDate'             => 2,
        'invoiceType'         => \NextelBR\Enumerators\NextelInvoiceTypes::BOLETO,
        'mode'                => Modes::ACTIVATION,
        'iccid'               => $faker->numerify('8955################'),
        'areaCode'            => $faker->numerify('##'),
        'operatorIdentifiers' => [
            'protocolo'    => \NextelBR\Tests\NextelBRTestBook::SUCCESS_PROTOCOL,
            'numeroPedido' => '20171111111111',
        ],
        'customer'            => [
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
        'status'              => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});

$factory->state(NextelBRControleBoleto::class, 'directDebit', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::NEXTEL;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::NEXTEL_CONTROLE_BOLETO;
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'dueDate'     => 2,
        'invoiceType' => \NextelBR\Enumerators\NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST,
        'mode'        => Modes::ACTIVATION,
        'iccid'       => $faker->numerify('8955################'),
        'promotion'   => random_int(5, 8),
        'directDebit' => [
            'checkingAccount' => '124560',
        ],
        'customer'    => [
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
            'neighborhood'     => $faker->streetSuffix,
            'local'            => $faker->streetName,
            'neighborhoodType' => 'teste',
            'city'             => 'teste',
            'state'            => 'SP'
        ],
        'status'      => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});

$factory->state(NextelBRControleBoleto::class, 'portability', function ($faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::NEXTEL;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::NEXTEL_CONTROLE_BOLETO;
    return [
        'sector'              => $sector,
        'operator'            => $operator,
        'operation'           => $operation,
        'dueDate'             => 2,
        'invoiceType'         => \NextelBR\Enumerators\NextelInvoiceTypes::BOLETO,
        'mode'                => Modes::PORTABILITY,
        'iccid'               => $faker->numerify('8955################'),
        'promotion'           => random_int(5, 8),
        'directDebit'         => [
            'checkingAccount' => '124560',
        ],
        'operatorIdentifiers' => [
            'protocolo'    => \NextelBR\Tests\NextelBRTestBook::SUCCESS_PROTOCOL,
            'numeroPedido' => '20171111111111',
        ],
        "portedNumber"        => $faker->numerify('5511999990000'),
        'portability'         => [
            'fromOperatorId'  => $faker->numerify('#####'),
            'fromOperator'    => 'VIVO',
            'portabilityDate' => '2018-08-12',
        ],
        'customer'            => [
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => 'teste',
            'filiation'        => 'teste',
            'mainPhone'        => 'teste',
            'rg'               => 'teste',
            'number'           => 'teste',
            'zipCode'          => 'teste',
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'city'             => 'teste',
            'state'            => 'SP'
        ],
        'status'              => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});

$factory->state(NextelBRControleBoleto::class, 'device', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::NEXTEL;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::NEXTEL_CONTROLE_BOLETO;
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'dueDate'     => 2,
        'invoiceType' => \NextelBR\Enumerators\NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST,
        'mode'        => Modes::ACTIVATION,
        'imei'        => $faker->numerify('###############'),
        'iccid'       => $faker->numerify('8955################'),
        'promotion'   => random_int(5, 8),
        'directDebit' => [
            'checkingAccount' => '124560',
        ],
        'customer'    => [
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
            'neighborhood'     => $faker->streetSuffix,
            'local'            => $faker->streetName,
            'neighborhoodType' => 'teste',
            'city'             => 'teste',
            'state'            => 'SP'
        ],
        'status'      => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});
