<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Terms\Enums\TypeTermsEnum;
use Terms\Models\Term;

/**
 * @var Factory $factory
 * @return mixed[]
 */
$factory->define(Term::class, function (Faker $faker): array {
    return [
        'title' => $faker->name,
        'urlEmbed' => $faker->imageUrl(),
        'active' => 1,
        'type' => TypeTermsEnum::SALESMAN
    ];
});

/** @return mixed[] */
$factory->state(Term::class, 'salesman', function (Faker $faker): array {
    return [
        'title' => $faker->name,
        'urlEmbed' => $faker->imageUrl(),
        'active' => 1,
        'type' => TypeTermsEnum::SALESMAN
    ];
});

/** @return mixed[] */
$factory->state(Term::class, 'customer', function (Faker $faker): array {
    return [
        'title' => $faker->name,
        'urlEmbed' => $faker->imageUrl(),
        'active' => 1,
        'type' => TypeTermsEnum::CUSTOMER
    ];
});

/** @return mixed[] */
$factory->state(Term::class, 'inactive', function (Faker $faker): array {
    return [
        'title' => $faker->name,
        'urlEmbed' => $faker->imageUrl(),
        'active' => 0,
        'type' => TypeTermsEnum::SALESMAN
    ];
});
