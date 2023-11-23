<?php

use Buyback\Providers\Faker\Smartphone;
use Faker\Generator as Faker;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;

$factory->define(DeviceOutSourced::class, static function (Faker $faker) {
    $faker->addProvider(new Smartphone($faker));
    $model     = $faker->model;
    $colorName = $faker->colorName;
    $storage   = $faker->randomElement(['32GB', '64GB', '128GB', '256GB']);

    return [
        'sku'     => uniqid(),
        'label'   => "{$model} {$colorName} {$storage}",
        'price'   => $faker->randomFloat(2, 0, 99999),
        'model'   => $model,
        'brand'   => $faker->brand,
        'color'   => $colorName,
        'storage' => $storage
    ];
});
