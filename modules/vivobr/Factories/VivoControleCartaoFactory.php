<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use MongoDB\BSON\ObjectId;
use VivoBR\Models\VivoControleCartao;

$factory->define(VivoControleCartao::class, function (Faker $faker) {
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
        'customer'    => [
            'email'     => $faker->email,
            'firstName' => $faker->firstName,
            'lastName'  => $faker->lastName,
            'cpf'       => $faker->unique()->cpf(false),
            'mainPhone' => '+55981745995',
        ]
    ];
});

$factory->state(VivoControleCartao::class, 'without_customer', function () {
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'VIVO';
    $operation = 'CONTROLE';
    return [
        'sector'    => $sector,
        'operator'  => $operator,
        'operation' => $operation,
    ];
});
