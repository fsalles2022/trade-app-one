<?php

namespace TradeAppOne\Tests\Unit\Domain\Adapters;

use TradeAppOne\Domain\Adapters\MergeRoleWithPermissions;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\TestCase;

class MergeRoleWithPermissionsTest extends TestCase
{
    /** @test */
    public function should_return_array_roles_with_permissions()
    {
        $permission = factory(Permission::class)->create([
            'client' => 'WEB'
        ]);

        $roleParent = (new RoleBuilder())->build();
        $roleParent->stringPermissions()->attach($permission);

        $role = ((new RoleBuilder)->withParent($roleParent)->build());
        $role->stringPermissions()->attach($permission);

        $collection = $role->get();
        $adapter = (new MergeRoleWithPermissions($collection))->adapt();

        $this->assertArrayHasKey('id', $adapter[1]);
        $this->assertArrayHasKey('name', $adapter[1]);
        $this->assertArrayHasKey('slug', $adapter[1]);

        $this->assertArrayHasKey('id', $adapter[1]['permissions'][0]);
        $this->assertArrayHasKey('label', $adapter[1]['permissions'][0]);
        $this->assertArrayHasKey('slug', $adapter[1]['permissions'][0]);
    }
}