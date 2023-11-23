<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Factory;
use TimBR\Models\TimBRControleFlex;
use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TimBR\Tests\TimBRTestBook;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

/** @var Factory $factory */
$factory->define(TimBRControleFlex::class, function (Faker $faker): array {
    $faker->addProvider(new pt_BR\Person($faker));

    return [
        'sector'           => Operations::TELECOMMUNICATION,
        'operator'         => Operations::TIM,
        'operation'        => Operations::TIM_CONTROLE_FLEX,
        'areaCode'         => 11,
        'iccid'            => $faker->numerify('8955################'),
        'dueDate'          => 2,
        'invoiceType'      => 'EMAIL',
        'creditCard'       => [
            'token' => $faker->numerify('4242################'),
            'cvv'   => $faker->numerify('###'),
        ],
        'eligibilityToken' => $faker->numerify('2018####################'),
        'mode'             => Modes::ACTIVATION,
        'product'          => random_int(5, 8),
        'promoter'         => [
            'cpf' => $faker->unique()->cpf(false)
        ],
        'customer'         => [
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
            'rgDate'           => $faker->date(),
            'rgState'          => 'teste',
            'number'           => $faker->numerify('###'),
            'zipCode'          => 'teste',
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'complement'       => 'Tradeup Group',
            'local'            => 'teste',
            'localId'          => 'teste',
            'city'             => 'teste',
            'state'            => 'SP',
            'country'          => 'Brasil'
        ],
        'status'           => ServiceStatus::SUBMITTED
    ];
});


$factory->state(TimBRControleFlex::class, 'userSuccess', function (Faker $faker): array {

    return [
        'sector'           => Operations::TELECOMMUNICATION,
        'operator'         => Operations::TIM,
        'operation'        => Operations::TIM_CONTROLE_FLEX,
        'areaCode'         => 11,
        'iccid'            => $faker->numerify('8955################'),
        'dueDate'          => 2,
        'invoiceType'      => 'EMAIL',
        'creditCard'       => [
            'token' => $faker->numerify('4242################'),
            'cvv'   => $faker->numerify('###'),
        ],
        'eligibilityToken' => $faker->numerify('2018####################'),
        'mode'             => Modes::ACTIVATION,
        'product'          => random_int(5, 8),
        'customer'         => [
            'email'            => $faker->email,
            'firstName'        => 'teste',
            'lastName'         => 'teste',
            'cpf'              => TimBRTestBook::SUCCESS_CUSTOMER,
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
            'rgDate'           => $faker->date(),
            'rgState'          => 'teste',
            'number'           => $faker->numerify('###'),
            'zipCode'          => 'teste',
            'neighborhood'     => 'teste',
            'neighborhoodType' => 'teste',
            'local'            => 'teste',
            'localId'          => 'teste',
            'city'             => 'teste',
            'state'            => 'SP',
            'country'          => 'Brasil'
        ],
        'status'           => ServiceStatus::SUBMITTED
    ];
});
