<?php

use Authorization\Models\AvailableRedirect;
use Authorization\Models\Integration;
use Faker\Generator as Faker;

$factory->define(AvailableRedirect::class, static function (Faker $faker){
    return [
        'integrationId' => factory(Integration::class)->create(),
        'redirectUrl' => $faker->url,
        'defaultUrl' => $faker->boolean,
        'routeKey' => $faker->slug
    ];
});