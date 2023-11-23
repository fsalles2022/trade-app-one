<?php

declare(strict_types=1);

use Bulletin\Models\Bulletin;
use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;

$factory->define(Bulletin::class, static function (Faker $faker) {
    $faker->addProvider(new pt_BR\Person($faker));

    $date    = $faker->date();
    $network = (new NetworkBuilder())->build();

    return [
        'title' => $faker->company,
        'description' => $faker->realText(),
        'networkId' => $network->id,
        'status' => $faker->boolean(),
        'urlImage' => 's3/comunicado/image.jpeg',
        'initialDate' => $date,
        'finalDate' => $date
    ];
});
