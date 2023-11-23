<?php

use Faker\Generator as Faker;

$factory->define(\Buyback\Models\Evaluation::class, function (Faker $faker) {
    return [
        'goodValue'    => rand(1, 3000),
        'averageValue' => rand(1, 3000),
        'defectValue'  => rand(1, 3000),
    ];
});
