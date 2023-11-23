<?php

use Buyback\Providers\Faker\Smartphone;
use TradeAppOne\Domain\Models\Tables\Device;
use Faker\Generator as Faker;

$factory->define(Device::class, function (Faker $faker) {
    $faker->addProvider(new Smartphone($faker));
    $model     = $faker->model;
    $colorName = $faker->colorName;
    $storage   = $faker->randomElement(["32GB", "64GB", "128GB", "256GB"]);

    return [
        'label'       => "$model $colorName $storage",
        'model'       => $model,
        'brand'       => $faker->brand,
        'color'       => $colorName,
        'storage'     => $storage,
        'imageFront'  => $faker->imageUrl(),
        'imageBehind' => $faker->imageUrl(),
        'type'        => $faker->randomElement(Device::DEVICE_TYPES),
        'material'    => null,
        'caseSize'    => null
    ];
});
