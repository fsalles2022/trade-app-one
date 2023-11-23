<?php

use Discount\Models\DeviceDiscount;
use Faker\Generator as Faker;

$factory->define(DeviceDiscount::class, function (Faker $faker) {
    return [
        'discount'   => $faker->randomFloat(2, 0, 1000),
    ];
});