<?php

use Faker\Generator as Faker;
use TradeAppOne\Domain\Models\Tables\ImportHistory;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

$factory->define(ImportHistory::class, function (Faker $faker) {
    $user = (new UserBuilder())->build();

    $types   = ['DEVICES', 'USERS', 'POINTS_OF_SALE', 'GOALS'];
    $sources = ['SUCCESS', 'ERROR'];

    $var    = $types[array_rand($types)];
    $status = $sources[array_rand($sources)];

    $errorFile = ($status === 'ERROR') ? 'https://s3.amazonaws.com/tradeapp-one/importable/' .
        $user->getNetwork()->slug . '-error.csv' : null;


    return [
        'type' => $var,
        'inputFile'  => 'https://s3.amazonaws.com/tradeapp-one/importable/' . $user->getNetwork()->slug . '.csv',
        'outputFile' => $errorFile,
        'status' => $status,
        'userId' => $user->id
    ];
});
