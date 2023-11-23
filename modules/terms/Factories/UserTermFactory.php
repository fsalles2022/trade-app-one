<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Terms\Enums\StatusUserTermsEnum;
use Terms\Models\UserTerm;

/**
 * @var Factory $factory
 * @return mixed[]
 */
$factory->define(UserTerm::class, function (Faker $faker): array {
    return [
        'userId' => $faker->randomNumber(1),
        'termId' => $faker->randomNumber(1),
        'status' => StatusUserTermsEnum::VIEWED,
    ];
});

/** @return mixed[] */
$factory->state(UserTerm::class, 'checked', function (Faker $faker): array {
    return [
        'userId' => $faker->randomNumber(1),
        'termId' => $faker->randomNumber(1),
        'status' => StatusUserTermsEnum::CHECKED,
    ];
});

/** @return mixed[] */
$factory->state(UserTerm::class, 'viewed', function (Faker $faker): array {
    return [
        'userId' => $faker->randomNumber(1),
        'termId' => $faker->randomNumber(1),
        'status' => StatusUserTermsEnum::VIEWED,
    ];
});
