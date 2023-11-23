<?php

use Buyback\Models\Question;
use Faker\Generator as Faker;

$factory->define(Question::class, function (Faker $faker) {
    return [
        'question' => $faker->word,
        'weight'   => rand(1, 2000),
        'order'    => $faker->unique()->randomNumber(2),
        'blocker'  => $faker->boolean
    ];
});

$factory->state(Question::class, 'non_blocker', function () {
    return ['blocker' => false];
});
