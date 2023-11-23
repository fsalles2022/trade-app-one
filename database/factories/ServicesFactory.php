<?php

use Faker\Generator as Faker;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Service;

$factory->define(Service::class, static function(Faker $faker){

    $service   = array_rand(Operations::SECTORS);
    $operator  = array_rand(Operations::SECTORS[$service]);
    $operation = array_rand(Operations::SECTORS[$service][$operator]);

    if($service === Operations::TELECOMMUNICATION) {
        $service = Operations::LINE_ACTIVATION;
    }

    return [
        'sector'    => $service,
        'operator'  => $operator,
        'operation' => $operation,
    ];
});
