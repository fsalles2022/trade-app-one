<?php


use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use McAfee\Models\McAfeeMobileSecurity;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

$factory->define(McAfeeMobileSecurity::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    return [
        'sector'           => Operations::SECURITY,
        'operator'         => Operations::MCAFEE,
        'operation'        => Operations::MCAFEE_MOBILE_SECURITY,
        'mode'             => Modes::ACTIVATION,
        'product'          => '1341',
        'price'            => '250.80',
        'productKey'       => 'r0EHYt5McOPElSK2hUEsvr2yB3S/xbHcR7XXBPqtKJXZqDP/3p9KV29wnmQqs+wi',
        'quantity'         => '1',
        'label'            => 'McAfee Mobile Security',
        'payment'          => [
            'gatewayReference' => '5d1a7260ce00a',
            'gatewayTransactionId' => '25656057-A5D1-73A1-0CE9-3B75C61974B4',
        ],
        'license' => [
            'mcAfeeReference' => '12314234234',
            'mcAfeeActivationCode' => 'ABCD',
            'mcAfeeProductKey' => 'r0EHYt5McOPElSK2hUEsvr2yB3S/xbHcR7XXBPqtKJXZqDP/3p9KV29wnmQqs+wi',
            'quantity'         => '1',
        ],
        'customer'         => [
            'cpf'       => $faker->unique()->cpf(false),
            'firstName' => $faker->firstName,
            'lastName'  => $faker->lastName,
            'email'     => $faker->email,
            'mainPhone' => $faker->numerify('+55###########'),
            'password'  => $faker->password
        ],
        'status'           => ServiceStatus::PENDING_SUBMISSION
    ];
});
