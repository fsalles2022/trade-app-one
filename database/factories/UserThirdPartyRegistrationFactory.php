<?php

use Faker\Generator as Faker;
use TradeAppOne\Domain\Models\Tables\UserPendingRegistration;

$factory->define(UserPendingRegistration::class, function (Faker $faker) {
    return [
        'operator' => \TradeAppOne\Domain\Enumerators\Operations::OI
    ];
});
