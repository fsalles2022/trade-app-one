<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TimBR\Models\TimBRControleFatura;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

$factory->define(TimBRControleFatura::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = Operations::TIM;
    $operation = Operations::TIM_CONTROLE_FATURA;
    return [
        'serviceTransaction'  => '202103151804189631-0',
        'sector'              => $sector,
        'operator'            => $operator,
        'operation'           => $operation,
        'areaCode'            => 11,
        'iccid'               => $faker->numerify('8955################'),
        'dueDate'             => 2,
        'invoiceType'         => 'Fatura',
        'mode'                => Modes::ACTIVATION,
        'product'             => '1-118LLUU',
        'label'               => $faker->text(),
        'billType'            => 'Resumida',
        'authenticate'        => [
            'linkId' => '123',
            'linkUrl' => 'https://brscan.authenticate.user/abaBUhpi2390HJdnl',
        ],
        'timProtocolSearchTries' => 1,
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
            'rgDate'           => $faker->date('Y-m-d'),
            'rgState'          => 'SP',
            'number'           => $faker->numerify('11#########'),
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'local'            => 'teste',
            'localId'          => 'teste',
            'city'             => 'teste',
            'state'            => 'SP',
            'zipCode'          => '06454000',
            'country'          => 'Brasil'
        ],
        'status'              => ServiceStatus::SUBMITTED
    ];
});

$factory->state(TimBRControleFatura::class, 'without_customer', function () {
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_CONTROLE_FATURA;
    return [
        'sector'    => $sector,
        'operator'  => $operator,
        'operation' => $operation,
        'promotion' => random_int(5, 8),
        'authenticate'        => [
            'linkId' => '123',
            'linkUrl' => 'https://brscan.authenticate.user/abaBUhpi2390HJdnl',
        ],
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


$factory->state(TimBRControleFatura::class, 'portability', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_CONTROLE_FATURA;
    return [
        'sector'       => $sector,
        'operator'     => $operator,
        'operation'    => $operation,
        'portedNumber' => 1199999999,
        'iccid'        => $faker->numerify('8955################'),
        'dueDate'      => 2,
        'invoiceType'  => 'EMAIL',
        'mode'         => Modes::PORTABILITY,
        'promotion'    => random_int(5, 8),
        'authenticate'        => [
            'linkId' => '123',
            'linkUrl' => 'https://brscan.authenticate.user/abaBUhpi2390HJdnl',
        ],
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

$factory->state(TimBRControleFatura::class, 'migration', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_CONTROLE_FATURA;
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'msisdn'      => 1199999999,
        'iccid'       => '',
        'dueDate'     => 2,
        'invoiceType' => 'EMAIL',
        'mode'        => Modes::MIGRATION,
        'promotion'   => random_int(5, 8),
        'authenticate'        => [
            'linkId' => '123',
            'linkUrl' => 'https://brscan.authenticate.user/abaBUhpi2390HJdnl',
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

$factory->state(TimBRControleFatura::class, 'success_customer', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_CONTROLE_FATURA;
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'msisdn'      => 1199999999,
        'iccid'       => '',
        'dueDate'     => 2,
        'invoiceType' => 'EMAIL',
        'mode'        => Modes::MIGRATION,
        'promotion'   => random_int(5, 8),
        'authenticate'        => [
            'linkId' => '123',
            'linkUrl' => 'https://brscan.authenticate.user/abaBUhpi2390HJdnl',
        ],
        'customer'    => [
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => '00000009652',
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

$factory->state(TimBRControleFatura::class, 'debito_automatico', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_CONTROLE_FATURA;
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'areaCode'    => 11,
        'iccid'       => $faker->numerify('8955################'),
        'dueDate'     => 2,
        'invoiceType' => \TimBR\Enumerators\TimBRInvoiceTypes::DEBITO_AUTOMATICO,
        'mode'        => Modes::ACTIVATION,
        'product'     => random_int(5, 8),
        'directDebit' => [
            'bankId' => [
                'id' => '123',
                'label' => 'Banco Teste'
            ],
            'checkingAccount' => '12',
            'agency' => '12',
        ],
        'authenticate'        => [
            'linkId' => '123',
            'linkUrl' => 'https://brscan.authenticate.user/abaBUhpi2390HJdnl',
        ],
        'customer'    => [
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
            'country'          => 'Brasil'
        ],
        'status'      => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});

$factory->state(TimBRControleFatura::class, 'loyalty', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_CONTROLE_FATURA;
    return [
        'sector'              => $sector,
        'operator'            => $operator,
        'operation'           => $operation,
        'areaCode'            => 11,
        'iccid'               => $faker->numerify('8955################'),
        'dueDate'             => 2,
        'invoiceType'         => 'EMAIL',
        'mode'                => Modes::ACTIVATION,
        'product'             => '1-118LLUU',
        'operatorIdentifiers' => [
            'protocol' => "231872389"
        ],
        'authenticate'        => [
            'linkId' => '123',
            'linkUrl' => 'https://brscan.authenticate.user/abaBUhpi2390HJdnl',
        ],
        'loyalty' => ['id' => '1-10RFAPJ'],
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

$factory->state(TimBRControleFatura::class, 'withService', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = \TradeAppOne\Domain\Enumerators\Operations::TIM;
    $operation = \TradeAppOne\Domain\Enumerators\Operations::TIM_CONTROLE_FATURA;
    return [
        'sector'              => $sector,
        'operator'            => $operator,
        'operation'           => $operation,
        'areaCode'            => 11,
        'iccid'               => $faker->numerify('8955################'),
        'dueDate'             => 2,
        'invoiceType'         => 'EMAIL',
        'mode'                => Modes::ACTIVATION,
        'product'             => '1-11L5P25',
        'operatorIdentifiers' => [
            'protocol' => "231872389"
        ],
        'authenticate'        => [
            'linkId' => '123',
            'linkUrl' => 'https://brscan.authenticate.user/abaBUhpi2390HJdnl',
        ],
        'loyalty' => ['id' => '1-1D83LPL'],
        'service' => [
            [
                'id' => '1-1EH21XO'
            ]
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
