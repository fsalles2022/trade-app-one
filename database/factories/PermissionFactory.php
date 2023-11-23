<?php

use Faker\Generator as Faker;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\DashboardPermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

$factory->define(Permission::class, function (Faker $faker) {
    return [
        'label'  => $faker->jobTitle,
        'slug'   => DashboardPermission::getFullName(PermissionActions::VIEW),
        'client' => SubSystemEnum::API
    ];
});

$factory->state(Permission::class, 'web', function (Faker $faker) {
    return [
        'label'  => $faker->jobTitle,
        'slug'   => DashboardPermission::getFullName(PermissionActions::VIEW),
        'client' => SubSystemEnum::WEB
    ];
});

$factory->state(Permission::class, 'app', function (Faker $faker) {
    return [
        'label'  => $faker->jobTitle,
        'slug'   => DashboardPermission::getFullName(PermissionActions::VIEW),
        'client' => SubSystemEnum::APP
    ];
});

$factory->state(Permission::class, 'any', function (Faker $faker) {
    return [
        'label'  => $faker->jobTitle,
        'slug'   => $faker->slug . '.' . $faker->slug,
        'client' => SubSystemEnum::APP
    ];
});