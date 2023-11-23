<?php

use Faker\Generator as Faker;
use TradeAppOne\Domain\Models\Tables\ServiceOption;

$factory->define(ServiceOption::class, static function(Faker $faker){

    return [
        'action' => 'DEFAULT_ACTION'
    ];
});

