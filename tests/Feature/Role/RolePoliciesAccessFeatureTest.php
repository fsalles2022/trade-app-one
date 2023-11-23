<?php

namespace TradeAppOne\Tests\Feature\Role;

use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\CustomUsersRole;
use TradeAppOne\Tests\TestCase;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\RolePermission;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Tests\Helpers\AuthHelper;

class RolePoliciesAccessFeatureTest extends TestCase
{
    use AuthHelper, CustomUsersRole;

    const ROUTE_STORE = '/roles/store';
    const ROUTE_EDIT  = '/roles/edit/';
    
    /** @test */
    public function post_should_return_401_when_user_has_not_permission_create()
    {
        $userAuth = $this->createUserWithPermissions();

        $newRole = [
            'name'            => 'new role',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function post_should_return_403_when_user_has_not_permissions_edited_in_wallet()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::CREATE)]);

        $permissions = factory(Permission::class)->create();

        $newRole = [
            'name'            => 'new role',
            'permissionsSlug' => [$permissions->slug],
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function post_should_return_403_when_user_not_belong_same_network_of_new_role()
    {
        $enumDefineParent = RolePermission::getFullName(RolePermission::DEFINE_PARENT);
        $enumCreateRole   = RolePermission::getFullName(PermissionActions::CREATE);

        $userAuth = $this->createUserWithPermissions([$enumCreateRole, $enumDefineParent]);

        $network = factory(Network::class)->create();
        $roleParent = (new RoleBuilder())->withNetwork($network)->build();

        $newRole = [
            'name'            => 'new role',
            'parent'          => $roleParent->id,
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $network->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function post_should_return_409_when_role_slug_already_exists()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::CREATE)]);
        $userAuth->role->update(['slug' => 'developer-tradeup']);

        $newRole = [
            'name'            => 'developer',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $response->assertStatus(Response::HTTP_CONFLICT);
    }

    /** @test */
    public function put_should_return_403_when_user_has_not_permission_under_role()
    {
        $network = factory(Network::class)->create();

        factory(Network::class)->create([
            'slug' => 'tradeup-group'
        ]);

        $permissions = factory(Permission::class)->create([
            'slug' => RolePermission::getFullName(PermissionActions::EDIT)
        ]);

        $roleHigher = (new RoleBuilder())
            ->withNetwork($network)
            ->build();

        $roleLower  = (new RoleBuilder())
            ->withNetwork($network)
            ->withParent($roleHigher)
            ->build();

        $userAuth = (new UserBuilder())
            ->withNetwork($network)
            ->withPermissions([$permissions])
            ->withRole($roleLower)
            ->build();


        $editRole = [
            'name'            => 'new role',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->put(self::ROUTE_EDIT.$roleHigher->id, $editRole);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function put_should_return_403_when_user_has_not_permission_edit()
    {
        $userAuth = $this->createUserWithPermissions();
        $roleEditable = $this->createEditableRole($userAuth->getNetwork());

        $editRole = [
            'name'            => 'new role',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->put(self::ROUTE_EDIT.$roleEditable->id, $editRole);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function put_should_maintain_the_permissions_that_existed_in_edit()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::EDIT)]);

        $roleEditable       = $this->createEditableRole($userAuth->getNetwork());
        $permissionRoleEdit = factory(Permission::class)->create(['slug' => 'OTHER']);
        $roleEditable->stringPermissions()->attach($permissionRoleEdit);

        $permissions = $userAuth->role->stringPermissions->pluck('slug')->merge('OTHER')->toArray();

        $editRole = [
            'name'            => 'new role',
            'permissionsSlug' => $permissions,
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->put(self::ROUTE_EDIT.$roleEditable->id, $editRole);

        $this->assertCount(2, $roleEditable->fresh()->stringPermissions);
        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function put_should_return_404_when_role_not_exists()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::EDIT)]);

        $editRole = [
            'name'            => 'new role',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->put(self::ROUTE_EDIT.'3324', $editRole);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function put_should_return_403_when_user_not_belongs_network_of_role()
    {
        $roleEditable = (new RoleBuilder())->build();

        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::EDIT)]);
        $userAuth->role->update(['parent' => $roleEditable->id]);

        $editRole = [
            'name'            => 'new role',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->put(self::ROUTE_EDIT.$roleEditable->id, $editRole);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
    
    /** @test */
    public function should_save_parent_default_when_user_not_permission_to_define_parent()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::CREATE)]);

        $newRole = [
            'name'            => 'new role',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

       $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $this->assertDatabaseHas('roles', ['parent' => $userAuth->role->id]);
    }

    /** @test */
    public function should_save_parent_parameter_when_user_define_parent()
    {
        $enumDefineParent = RolePermission::getFullName(RolePermission::DEFINE_PARENT);
        $enumCreateRole   = RolePermission::getFullName(PermissionActions::CREATE);

        $userAuth = $this->createUserWithPermissions([$enumCreateRole, $enumDefineParent]);

        $roleHelper = (new RoleBuilder())
            ->withNetwork($userAuth->getNetwork())
            ->withParent($userAuth->role)
            ->build();

        $newRole = [
            'name'            => 'new role',
            'parent'          => $roleHelper->id,
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $userAuth->getNetwork()->slug
        ];

        $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $this->assertDatabaseHas('roles', ['parent' => $roleHelper->id]);
    }

    /** @test */
    public function should_return_422_when_parent_not_belongs_to_network()
    {
        $enumDefineParent = RolePermission::getFullName(RolePermission::DEFINE_PARENT);
        $enumCreateRole   = RolePermission::getFullName(PermissionActions::CREATE);

        $userAuth = $this->createUserWithPermissions([$enumCreateRole, $enumDefineParent]);

        $roleParent = (new RoleBuilder())
            ->withNetwork($userAuth->getNetwork())
            ->withParent($userAuth->role)
            ->build();

        $network = (new NetworkBuilder())->build();

        $newRole = [
            'name'            => 'new role',
            'parent'          => $roleParent->id,
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug'     => $network->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::ROUTE_STORE, $newRole);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function put_should_return_409_when_role_edited_already_exist()
    {
        $userAuth     = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::EDIT)]);
        $roleEditable = $this->createEditableRole($userAuth->getNetwork());

        $newRole = (new RoleBuilder())->build();
        $newRole->update(['slug' => 'role-editable-tradeup']);

        $editRole = [
            'name' => 'role editable',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug' => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->put(self::ROUTE_EDIT . $roleEditable->id, $editRole);

        $response->assertStatus(Response::HTTP_CONFLICT);
    }
}