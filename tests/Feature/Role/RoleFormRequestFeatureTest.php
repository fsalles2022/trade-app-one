<?php

namespace TradeAppOne\Tests\Feature\Role;

use TradeAppOne\Tests\Helpers\Traits\CustomUsersRole;
use TradeAppOne\Tests\TestCase;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\RolePermission;
use TradeAppOne\Tests\Helpers\AuthHelper;

class RoleFormRequestFeatureTest extends TestCase
{
    use AuthHelper, CustomUsersRole;

    const ROUTE_STORE = '/roles/store';

    /** @test */
    public function post_should_return_422_when_name_invalid()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::CREATE)]);

        $newRole = [
            'name'            => 'new-@role123',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_return_422_when_parent_invalid()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::CREATE)]);

        $newRole = [
            'name'            => 'new role',
            'parent'          => 'PARENT-INVALID',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_return_422_when_parent_not_exists()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::CREATE)]);

        $newRole = [
            'name'            => 'new role',
            'parent'          => '5',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];
        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_return_422_when_permission_invalid()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::CREATE)]);

        $newRole = [
            'name'            => 'new role',
            'permissionsSlug' => 'permission-invalid',
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_return_422_when_network_invalid()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::CREATE)]);

        $newRole = [
            'name'            => 'new role',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => 'network-invalid'
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}