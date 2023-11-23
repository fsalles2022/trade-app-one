<?php

use Core\HandBooks\Models\Handbook;
use Faker\Generator as Faker;
use TradeAppOne\Domain\Enumerators\Files\FileTypes;
use TradeAppOne\Domain\Enumerators\FilterModes;
use TradeAppOne\Domain\Enumerators\Operations;

$factory->define(Handbook::class, static function (Faker $faker) {
    return [
        'title'       => $faker->title,
        'description' => $faker->word,
        'file'        => $faker->url,
        'type'        => FileTypes::DOCUMENT,
        'module'      => Operations::COURSES,
        'category'    => $faker->slug,
        'networksFilterMode' => FilterModes::ALL,
        'rolesFilterMode' => FilterModes::ALL
    ];
});
