<?php

namespace TradeAppOne\Tests\Unit\Domain\Components;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Permissions\PermissionsWrapper;
use TradeAppOne\Domain\Enumerators\ContextEnum;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\RecoveryPermission;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Tests\TestCaseUnit;

class PermissionsWrapperTest extends TestCaseUnit
{
    /** @test */
    public function should_map_permissions()
    {
        $assertPermission      = [SubSystemEnum::API => [SalePermission::NAME => [SalePermission::CREATE]]];
        $permissionsCollection = new Collection([
            [
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(PermissionActions::CREATE)
            ]
        ]);

        $result = PermissionsWrapper::wrap($permissionsCollection);

        self::assertEquals($assertPermission, $result);
    }

    /** @test */
    public function should_map_3_level_permissions()
    {
        $assertPermission      = [SubSystemEnum::API => [SalePermission::NAME => []]];
        $permissionsCollection = new Collection([
            [
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::NAME
            ]
        ]);

        $result = PermissionsWrapper::wrap($permissionsCollection);

        self::assertEquals($assertPermission, $result);
    }

    /** @test */
    public function should_map_4_level_permissions()
    {
        $assertPermission      = [SubSystemEnum::API => [RecoveryPermission::NAME => [ContextEnum::CONTEXT_ALL]]];
        $permissionsCollection = new Collection([
            [
                'client' => SubSystemEnum::API,
                'slug'   => RecoveryPermission::getFullName(ContextEnum::CONTEXT_ALL) . '.ANOTHER'
            ]
        ]);

        $result = PermissionsWrapper::wrap($permissionsCollection);

        self::assertEquals($assertPermission, $result);
    }

    /** @test */
    public function should_groupby_sale_and_user()
    {
        $permissions = collect();
        $permission1 = factory(Permission::class)->make(['slug' => 'SALE.ANY']);
        $permissions->push($permission1);
        $permission2 = factory(Permission::class)->make(['slug' => 'SALE.ANY2']);
        $permissions->push($permission2);
        $permission3 = factory(Permission::class)->make(['slug' => 'USER.ANY']);
        $permissions->push($permission3);

        $result = PermissionsWrapper::groupPermissionsByModule($permissions);
        self::assertCount(2, $result->get('SALE'));
        self::assertCount(1, $result->get('USER'));
    }
}
