<?php

namespace TradeAppOne\Tests\Unit\Domain\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\RolePermission;
use TradeAppOne\Domain\Services\RolePermissionService;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\TestCase;

class RolePermissionServiceTest extends TestCase
{
    protected $rolePermissionService;

    protected function setUp()
    {
        parent::setUp();
        $roleOne = (new RoleBuilder())->build();
        $roleTwo = (new RoleBuilder())->build();
        $permissionOne = factory(Permission::class)->states('any')->create();
        $permissionTwo = factory(Permission::class)->states('any')->create();

        $permissionOne->role()->attach($roleOne);
        $permissionTwo->role()->attach($roleOne);
        $permissionOne->role()->attach($roleTwo);
        $permissionTwo->role()->attach($roleTwo);

        $this->rolePermissionService = resolve(RolePermissionService::class);
    }

    /** @test */
    public function should_return_a_collection()
    {
        $rolePermissions = RolePermission::all();
        $entriesDeleted = $this->rolePermissionService->removeDuplicate($rolePermissions);
        $this->assertInstanceOf(Collection::class, $entriesDeleted);
    }

    /** @test */
    public function should_return_a_empty_collection_when_not_has_duplicate_items()
    {
        $rolePermissions = RolePermission::all();
        $entriesDeleted = $this->rolePermissionService->removeDuplicate($rolePermissions);
        $this->assertEmpty($entriesDeleted);
    }

    /** @test */
    public function should_return_a_collection_with_3_role_permission_when_exists_duplicate_items()
    {
        $permission = Permission::query()->first();
        $role = Role::query()->first();
        $permission->role()->attach($role);
        $permission->role()->attach($role);
        $permission->role()->attach($role);

        $rolePermissions = RolePermission::all();
        $entriesDeleted = $this->rolePermissionService->removeDuplicate($rolePermissions);
        $this->assertCount(3, $entriesDeleted);
    }
}
