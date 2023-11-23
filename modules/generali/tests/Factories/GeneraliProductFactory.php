<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use Generali\Models\GeneraliProduct;
use TradeAppOne\Domain\Models\Tables\Device;

$factory->define(GeneraliProduct::class, static function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));

    return [
        'slug' => Device::SMARTPHONE_TYPE,
        'startingTrack' => 700.01,
        'finalTrack' => 800.00,
        'twelveMonthsCode' => 10501008,
        'twentyFourMonthsCode' => 20501008,
        'twelveMonthsPrice' => 56.37,
        'twentyFourMonthsPrice' => 95.82
    ];
});
