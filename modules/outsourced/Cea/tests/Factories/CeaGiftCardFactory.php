<?php
use Faker\Generator as Faker;
use Outsourced\Cea\Hooks\CeaHooks;
use Outsourced\Cea\Models\CeaGiftCard;

$factory->define(CeaGiftCard::class, static function (Faker $faker) {
    return [
        'code' => $faker->randomNumber(9),
        'partner' => CeaHooks::PARTNER_TRADE_IN
    ];
});
