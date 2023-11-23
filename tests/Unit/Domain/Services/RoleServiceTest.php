<?php

namespace TradeAppOne\Tests\Unit\Domain\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Exportables\RoleExportable;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\PermissionRepository;
use TradeAppOne\Domain\Repositories\Collections\RoleRepository;
use TradeAppOne\Domain\Services\RoleService;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class RoleServiceTest extends TestCase
{
    /** @test */
    public function should_return_false_when_call_role_make_sale()
    {
        $mockRoleRepository        = \Mockery::mock(RoleRepository::class);
        $mockPermissionRepository  = \Mockery::mock(PermissionRepository::class);
        $roleService = new RoleService($mockRoleRepository, $mockPermissionRepository);

        $roleMocked              = \Mockery::mock(Role::class)->makePartial();
        $roleMocked->permissions = [];

        $received = $roleService->roleMakeSales($roleMocked);
        $this->assertFalse($received);
    }

    /** @test */
    public function should_return_true_when_call_role_make_sale()
    {
        $mockRoleRepository        = \Mockery::mock(RoleRepository::class);
        $mockPermissionRepository  = \Mockery::mock(PermissionRepository::class);
        $roleService = new RoleService($mockRoleRepository, $mockPermissionRepository);

        $roleMocked = \Mockery::mock(Role::class)->makePartial();

        $roleMocked->shouldReceive('getPermissionsAttribute')->andReturn([
            SubSystemEnum::WEB => [
                SalePermission::NAME => [PermissionActions::VIEW, PermissionActions::CREATE],
            ],
            SubSystemEnum::API => [
                SalePermission::NAME => [PermissionActions::VIEW, PermissionActions::CREATE],
            ]
        ]);

        $received = $roleService->roleMakeSales($roleMocked);
        $this->assertTrue($received);
    }


    /** @test */
    public function should_return_false_when_call_role_make_sale_with_invalid_permission()
    {
        $mockRoleRepository        = \Mockery::mock(RoleRepository::class);
        $mockPermissionRepository  = \Mockery::mock(PermissionRepository::class);
        $roleService = new RoleService($mockRoleRepository, $mockPermissionRepository);

        $roleMocked = \Mockery::mock(Role::class)->makePartial();

        $roleMocked->permissions = json_encode([
            SubSystemEnum::WEB => [],
            SubSystemEnum::API => []
        ]);

        $received = $roleService->roleMakeSales($roleMocked);
        $this->assertFalse($received);
    }

    /** @test */
    public function should_return_roles_with_user_has_authority_when_call_rolesThatUserHasAuthority()
    {
        $network = factory(Network::class)->create();
        $roleParent = (new RoleBuilder())->withNetwork($network)->build();

        $roleParent1 = (new RoleBuilder())->withNetwork($network)->withParent($roleParent)->build();
        (new RoleBuilder())->withNetwork($network)->withParent($roleParent1)->build();

        $user = (new UserBuilder())->withNetwork($network)->withRole($roleParent1)->build();

        $roleService = resolve(RoleService::class);
        $received    = $roleService->rolesThatUserHasAuthority($user);

        $this->assertCount(1, $received);
    }

    /** @test */
    public function should_return_with_network_and_paginated()
    {
        (new RoleBuilder())->build();
        $user     = (new UserBuilder())->build();

        $roleService = resolve(RoleService::class);
        $received    = $roleService->all($user);

        $this->assertArrayHasKey('network', $received->items()[0]->toArray());
        $this->assertInstanceOf(LengthAwarePaginator::class, $received);
    }

    /** @test */
    public function should_return_with_distinct_network_and_paginated()
    {
        $network  = factory(Network::class)->create();

        $roleAdmin = (new RoleBuilder())->withNetwork($network)->build();
        $roleUser  = (new RoleBuilder())->withNetwork($network)->withParent($roleAdmin)->build();
        $roleSon   = (new RoleBuilder())->withNetwork($network)->withParent($roleUser)->build();

        $user = (new UserBuilder())->withNetwork($network)->withRole($roleUser)->build();

        $roleService = resolve(RoleService::class);
        $received    = $roleService->all($user);

        $this->assertEquals(1, $received->count());
    }
}
