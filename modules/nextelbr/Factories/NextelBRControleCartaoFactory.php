<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Enumerators\Modes;

$factory->define(\NextelBR\Models\NextelBRControleCartao::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::NEXTEL;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::NEXTEL_CONTROLE_CARTAO;
    return [
        'sector'              => $sector,
        'operator'            => $operator,
        'operation'           => $operation,
        'dueDate'             => 2,
        'invoiceType'         => \NextelBR\Enumerators\NextelInvoiceTypes::CARTAO_DE_CREDITO,
        'mode'                => Modes::ACTIVATION,
        'iccid'               => $faker->numerify('8955################'),
        'areaCode'            => $faker->numerify('##'),
        'promotion'           => random_int(5, 8),
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
