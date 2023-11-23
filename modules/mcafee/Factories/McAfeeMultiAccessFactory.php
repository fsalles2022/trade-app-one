<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use Gateway\Enumerators\StatusPaymentTransaction;
use McAfee\Models\McAfeeMultiAccess;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;

$factory->define(McAfeeMultiAccess::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    return [
        'sector' => Operations::SECURITY,
        'operator' => Operations::MCAFEE,
        'operation' => Operations::MCAFEE_MULTI_ACCESS,
        'mode'             => Modes::ACTIVATION,
        'customer'         => [
            'cpf'       => $faker->unique()->cpf(false),
            'firstName'  => $faker->firstName,
            'lastName'  => $faker->lastName,
            'email'     => $faker->email,
            'mainPhone' => $faker->numerify('+55###########'),
            'password'  => '123456789'
        ],
        'license' => [
            'mcAfeeReference'      => '12314234234',
            'mcAfeeActivationCode' => 'ABCD',
            'mcAfeeProductKey'     => 'r0EHYt5McOPElSK2hUEsvr2yB3S/xbHcR7XXBPqtKJXZqDP/3p9KV29wnmQqs+wi',
            'quantity'             => '1',
        ],
        'payment' => [
            'gatewayReference'     => '5d1a7260ce00a',
            'gatewayTransactionId' => '25656057-A5D1-73A1-0CE9-3B75C61974B4',
            'gatewayStatus'        => StatusPaymentTransaction::STATUS_PAYMENT[6],
        ],
        'serviceTransaction' => '202112011551586074-0'
    ];
});
