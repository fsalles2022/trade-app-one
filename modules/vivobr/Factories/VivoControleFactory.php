<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use MongoDB\BSON\ObjectId;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use VivoBR\Models\VivoControle;

$factory->define(VivoControle::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'VIVO';
    $operation = 'CONTROLE';
    return [
        '_id'         => new ObjectId(),
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'msisdn'      => 1199999999,
        'product'     => 1432,
        'mode'        => 'ACTIVATION',
        'invoiceType' => 'EMAIL',
        'dueDate'     => 12,
        'status'      => ServiceStatus::PENDING_SUBMISSION,
        'customer'    => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => $faker->date('Y-m-d'),
            'filiation'        => $faker->name,
            'mainPhone'        => '+5511981745995',
            'secondaryPhone'   => '5511981745995',
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
            'state'            => 'SP',
        ]
    ];
});

$factory->state(VivoControle::class, 'portability', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'VIVO';
    $operation = 'CONTROLE';
    return [
        'sector'       => $sector,
        'operator'     => $operator,
        'operation'    => $operation,
        'portedNumber' => 1199999999,
        'product'      => 1432,
        'mode'         => 'PORTABILITY',
        'invoiceType'  => 'EMAIL',
        'dueDate'      => 12,
        'customer'     => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => $faker->date('Y-m-d'),
            'filiation'        => $faker->name,
            'mainPhone'        => '+5511981745995',
            'secondaryPhone'   => '+5511981745995',
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
            'state'            => 'SP',
        ]
    ];
});

$factory->state(VivoControle::class, 'msisdn', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'VIVO';
    $operation = 'CONTROLE';
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'operation'   => $operation,
        'msisdn'      => 1199999999,
        'product'     => 1432,
        'mode'        => 'ACTIVATION',
        'invoiceType' => 'EMAIL',
        'dueDate'     => 12,
        'customer'    => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => $faker->date('Y-m-d'),
            'filiation'        => $faker->name,
            'mainPhone'        => '5511981745995',
            'secondaryPhone'   => '5511981745995',
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
        ]
    ];
});

$factory->state(VivoControle::class, 'portability', function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'VIVO';
    $operation = 'CONTROLE';
    return [
        'sector'       => $sector,
        'operator'     => $operator,
        'operation'    => $operation,
        'portedNumber' => 1199999999,
        'product'      => 1432,
        'mode'         => 'ACTIVATION',
        'invoiceType'  => 'EMAIL',
        'areaCode'     => 12,
        'dueDate'      => 12,
        'customer'     => [
            'email'            => $faker->email,
            'firstName'        => $faker->firstName,
            'lastName'         => $faker->lastName,
            'cpf'              => $faker->unique()->cpf(false),
            'gender'           => 'M',
            'birthday'         => $faker->date('Y-m-d'),
            'filiation'        => $faker->name,
            'mainPhone'        => '+5511981745995',
            'secondaryPhone'   => '5511981745995',
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
        ]
    ];
});

$factory->state(VivoControle::class, 'without_customer', function () {
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'VIVO';
    $operation = 'CONTROLE';
    return [
        'sector'    => $sector,
        'operator'  => $operator,
        'operation' => $operation,
    ];
});
