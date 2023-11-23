<?php

use ClaroBR\Models\ControleFacil;
use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Enumerators\Modes;

$factory->define(ControleFacil::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $sector    = 'TELECOMMUNICATION';
    $operator  = 'CLARO';
    $operation = 'CONTROLE_FACIL';
    return [
        'sector'      => $sector,
        'operator'    => $operator,
        'promotion'   => random_int(5, 8),
        'operation'   => $operation,
        'mode'        => Modes::MIGRATION,
        'invoiceType' => 'EMAIL',
        'customer'    => [
            'email'     => $faker->email,
            'firstName' => $faker->firstName,
            'lastName'  => $faker->lastName,
            'cpf'       => $faker->unique()->cpf(false),
        ],
        'status'      => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
    ];
});
