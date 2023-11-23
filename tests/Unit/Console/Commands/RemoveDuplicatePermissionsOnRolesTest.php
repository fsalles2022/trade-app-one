<?php

use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Services\RolePermissionService;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\TestCase;

class RemoveDuplicatePermissionsOnRoles extends TestCase
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
    public function should_command_exists_in_artisan()
    {
        $this->artisan('custom:remove-duplicate-permissions');
    }
}
