<?php

use Faker\Generator as Faker;
use TradeAppOne\Domain\Enumerators\ContextEnum;
use TradeAppOne\Domain\Enumerators\Permissions\RecoveryPermission;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\Permissions\UserPermission;
use TradeAppOne\Domain\Enumerators\ScopeEnum;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Role;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name'                 => $faker->jobTitle,
        'slug'                 => microtime(),
        'level'                => ScopeEnum::EVERYTHING,
        'dashboardPermissions' => '{}',
    ];
});

$factory->state(Role::class, 'without_permissions', function () {
    return ['permissions' => ''];
});

$factory->state(Role::class, 'scope_own', function () {
    return ['level' => ScopeEnum::OWN];
});

$factory->state(Role::class, 'scope_own_point_of_sale', function () {
    return ['level' => ScopeEnum::OWN_POINT_OF_SALE];
});

$factory->state(Role::class, 'scope_own_hierarchy', function () {
    return ['level' => ScopeEnum::OWN_HIERARCHY];
});

$factory->state(Role::class, 'scope_own_network', function () {
    return ['level' => ScopeEnum::OWN_NETWORK];
});

$factory->state(Role::class, 'context_hierarchy', function () {
    return [
        'permissions' => json_encode([
            SubSystemEnum::API => [
                SalePermission::NAME     => [ContextEnum::CONTEXT_HIERARCHY],
                UserPermission::NAME     => [ContextEnum::CONTEXT_HIERARCHY],
                RecoveryPermission::NAME => [ContextEnum::CONTEXT_HIERARCHY]
            ]
        ])
    ];
});

$factory->state(Role::class, 'context_non_existent', function () {
    return [
        'permissions' => json_encode([
            SubSystemEnum::API => [
                SalePermission::NAME     => [ContextEnum::CONTEXT_NON_EXISTENT],
                UserPermission::NAME     => [ContextEnum::CONTEXT_NON_EXISTENT],
                RecoveryPermission::NAME => [ContextEnum::CONTEXT_NON_EXISTENT]
            ]
        ])
    ];
});

$factory->state(Role::class, 'admin', function (Faker $faker) {
    return [
        'name'                 => 'Administrator',
        'slug'                 => microtime(),
        'level'                => ScopeEnum::EVERYTHING,
        'dashboardPermissions' => '{}',
    ];
});

$factory->state(Role::class, 'salesman', function (Faker $faker) {
    return [
        'name'                 => 'Vendedor',
        'slug'                 => microtime(),
        'level'                => 1000,
        'dashboardPermissions' => '{}',
    ];
});

$factory->state(Role::class, 'manager', function (Faker $faker) {
    return [
        'name'                 => 'Manager',
        'slug'                 => microtime(),
        'level'                => 1000,
        'dashboardPermissions' => '{}',
    ];
});
