<?php

use Discount\Enumerators\DiscountStatus;
use Discount\Models\Discount;
use Faker\Generator as Faker;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;

$factory->define(Discount::class, function (Faker $faker) {
    $startDate = now()->subDay();
    $endDate   = now()->addDay();

    return [
        'title'      => $faker->sentence,
        'status'     => $faker->randomElement(ConstantHelper::getAllConstants(DiscountStatus::class)),
        'filterMode' => 'ALL',
        'networkId'  => \TradeAppOne\Domain\Models\Tables\Network::first()->id,
        'startAt'    => $startDate,
        'endAt'      => $endDate
    ];
});