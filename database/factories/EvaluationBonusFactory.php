<?php


use Buyback\Models\Evaluation;
use Buyback\Models\EvaluationsBonus;
use Faker\Generator as Faker;

$factory->define(EvaluationsBonus::class, static function (Faker $faker) {
    return [
        'evaluationId' => factory(Evaluation::class)->make()->id,
        'sponsor'  => $faker->word,
        'goodValue' => $faker->randomFloat(),
        'averageValue' => $faker->randomFloat(),
        'defectValue' => $faker->randomFloat()
    ];
});