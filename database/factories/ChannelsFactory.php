<?php


use Faker\Generator as Faker;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Models\Tables\Channel;

$factory->define(Channel::class, static function (Faker $faker) {
    return [
        'name' => $faker->name
    ];
});

$factory->state(Channel::class, Channels::DISTRIBUICAO, static function () {
    return [
        'name' => Channels::DISTRIBUICAO
    ];
});