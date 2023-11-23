<?php

use Authorization\Models\Integration;
use Faker\Generator as Faker;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Operator;
use TradeAppOne\Domain\Models\Tables\User;

$factory->define(Integration::class, static function (Faker $faker){
    return [
        'accessKey' => '3u6gX8djVNvZlKHaVeOB9EQTimh4z9JX',
        'networkId' => factory(Network::class)->create(),
        'operatorId' => factory(Operator::class)->create(),
        'userId' => factory(User::class)->create(),
        'credentialVerifyUrl' => 'http://api.redeinova.com.br/prosecutor-api-teste/api/trade-up/promotor',
        'subdomain' => 'inova',
        'client' => 'inova'
    ];
});