<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Channel;
use TradeAppOne\Domain\Models\Tables\Network;

$factory->define(Network::class, function (Faker $faker) {

    $faker->addProvider(new pt_BR\Company($faker));
    $faker->addProvider(new pt_BR\PhoneNumber($faker));

    $label   = $faker->name;
    $company = $faker->company;

    return [
        'slug'               => str_slug($label),
        'label'              => $label,
        'cnpj'               => $faker->cnpj(false),
        'tradingName'        => $company,
        'companyName'        => $company . ' ' . $faker->companySuffix,
        'telephone'          => $faker->cellphone,
        'zipCode'            => $faker->postcode,
        'local'              => $faker->streetName,
        'neighborhood'       => $faker->citySuffix,
        'state'              => 'SP',
        'number'             => $faker->buildingNumber,
        'city'               => $faker->city,
        'complement'         => $faker->secondaryAddress,
    ];
});

$factory->state(Network::class, 'without_available_services', function () {
    return [
        'availableServices' => json_encode([
            "LINE_ACTIVATION" => []
        ])
    ];
});

$factory->state(Network::class, 'with_available_services', function () {
    return [
        'availableServices' => json_encode([
            "LINE_ACTIVATION" => [
                Operations::OI    => [
                    Operations::OI_CONTROLE_BOLETO,
                    Operations::OI_CONTROLE_CARTAO
                ],
                Operations::TIM   => [
                    Operations::TIM_EXPRESS,
                    Operations::TIM_CONTROLE_FATURA
                ],
                Operations::VIVO  => [
                    Operations::VIVO_CONTROLE_CARTAO,
                    Operations::VIVO_CONTROLE
                ],
                Operations::CLARO => [
                    Operations::CLARO_CONTROLE_BOLETO,
                    Operations::CLARO_CONTROLE_FACIL
                ]
            ]
        ])
    ];
});

$factory->afterCreating(Network::class, static function ($network, $faker){
    $network->channels()->save(factory(Channel::class)->make());
});
