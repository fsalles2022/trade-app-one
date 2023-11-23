<?php

use Faker\Generator as Faker;
use Reports\Goals\Models\GoalType;

$factory->define(GoalType::class, function (Faker $faker) {
    return [
        'type'  => $faker->unique()->slug,
        'label' => $faker->text(10)
    ];
});