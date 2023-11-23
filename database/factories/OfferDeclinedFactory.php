<?php

use Buyback\Models\OfferDeclined;
use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Enumerators\Operations;

$factory->define(OfferDeclined::class, function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));
    $faker->addProvider(new pt_BR\PhoneNumber($faker));

    return [
        'customer.fullName' => $faker->name,
        'customer.mainPhone' => $faker->phoneNumber,
        'customer.email' => $faker->safeEmail,
        'device.price' => $faker->randomFloat(2, 5, 10),
        'device.note' => $faker->randomNumber(),
        'device.imei' => '441332435262435',
        'reason' => $faker->sentence,
        'operator' => Operations::TRADE_IN_MOBILE,
        'operation' => Operations::SALDAO_INFORMATICA
    ];
});
