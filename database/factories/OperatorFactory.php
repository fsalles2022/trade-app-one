<?php

use Faker\Generator as Faker;
use TradeAppOne\Domain\Models\Tables\Operator;

$factory->define(Operator::class, function (Faker $faker) {
    $company = $faker->company;
    return [
        'slug' => str_slug($company),
        'label' => $company,
        'availableServices' => '{"LINE_ACTIVATION": {"CLARO": ["CONTROLE_BOLETO", "CONTROLE_FACIL", "CLARO_POS", "CLARO_PRE"]}}'
    ];
});