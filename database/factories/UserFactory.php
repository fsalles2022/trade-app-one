<?php

use Faker\Generator as Faker;
use Faker\Provider\pt_BR;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

$factory->define(User::class, function (Faker $faker) {

    $faker->addProvider(new pt_BR\Person($faker));
    $faker->addProvider(new pt_BR\PhoneNumber($faker));

    return [
        'firstName' => $faker->firstName,
        'lastName'  => $faker->lastName,
        'email'     => $faker->unique()->safeEmail,
        'cpf'       => $faker->unique()->cpf(false),
        'birthday'  => $faker->date(),
        'areaCode'  => $faker->areaCode,
        'password'  => bcrypt('91910048'),
        'roleId'    => factory(Role::class)->create()
    ];
});

$factory->state(User::class, 'invalid_cpf', function () {
    return ['cpf' => '111111111'];
});

$factory->state(User::class, 'another_password', function () {
    return ['password' => bcrypt('Trade@2018')];
});

$factory->state(User::class, 'invalid_area_code', function () {
    return ['areaCodePrefix' => '111'];
});

$factory->state(User::class, 'user_inactive', function () {
    return ['activationStatusCode' => UserStatus::INACTIVE];
});

$factory->state(User::class, 'user_verified', function () {
    return ['activationStatusCode' => UserStatus::VERIFIED];
});

$factory->state(User::class, 'user_active', function () {
    return ['activationStatusCode' => UserStatus::ACTIVE];
});

$factory->state(User::class, 'add_points_of_sale', function () {
    $pointsOfSale = factory(PointOfSale::class, 3)->make()->toArray();

    $idMapper = function ($it) {

    };

    $ids = array_map($idMapper, $pointsOfSale);

    return ['pointsOfSale' => array_values($ids)];
});
