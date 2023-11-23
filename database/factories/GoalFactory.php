<?php

use Faker\Generator as Faker;
use Reports\Goals\Models\Goal;

$factory->define(Goal::class, function (Faker $faker) {
    return [
        'year'  => now()->year,
        'month' => now()->month,
        'goal'  => rand(1, 1000)
    ];
});