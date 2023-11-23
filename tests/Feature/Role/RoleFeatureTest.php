<?php

namespace TradeAppOne\Tests\Feature\Role;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\RolePermission;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\CustomUsersRole;
use TradeAppOne\Tests\TestCase;

class RoleFeatureTest extends TestCase
{
    use AuthHelper, CustomUsersRole;

    const PREFIX = '/roles/';

    /** @test */
    public function get_should_return_200_when_request_index_roles()
    {
        $userAuth = $this->createUserWithPermissions();
        factory(Role::class)->create([
            'networkId' => $userAuth->getNetwork()->id
        ]);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->json('GET', self::PREFIX);

        $response->assertJsonStructure(['data' => ['*' => ["id", "name", "slug", "level"]]]);
    }

    /** @test */
    public function get_should_return_200_when_request_role_by_id()
    {
        $userAuth = $this->createUserWithPermissions();
        $id     = $userAuth->role->id;

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->json('GET', self::PREFIX . $id);

        $response->assertJsonStructure(['*' => ['id', 'name', 'slug', 'network', 'permissions']]);
    }

    /** @test */
    public function post_should_return_404_when_change_valid_user_to_invalid_role()
    {
        $userAuth = $this->createUserWithPermissions();
        $cpf      = $userAuth->cpf;

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::PREFIX . "{str_random(6)}/users/{$cpf}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function post_should_return_404_when_change_invalid_user_to_valid_role()
    {
        $userAuth = $this->createUserWithPermissions();
        $slug     = $userAuth->role->slug;

        $user = factory(User::class)->make()->cpf;

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->json('POST', "{self::PREFIX} {$slug}/users/{$user}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function post_should_return_404_when_change_invalid_user_to_invalid_role()
    {
        $userAuth = $this->createUserWithPermissions();
        $user     = factory(User::class)->make()->cpf;

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::PREFIX . "{str_random(6)}/users/{$user}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function get_should_return_200_and_roles_that_user_has_permission()
    {
        $network = factory(Network::class)->create();
        factory(Role::class)->create([
            'networkId' => $network->id
        ]);

        $user = (new UserBuilder())->withNetwork($network)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get(self::PREFIX . 'user/logged');

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['*' => ["id", "name", "slug", "permissions"]]);
    }


    /** @test */
    public function post_should_return_201_when_create_role()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::CREATE)]);

        $newRole = [
            'name' => 'new role',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug' => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->post(self::PREFIX . 'store', $newRole);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function put_should_return_200_when_edited_role()
    {
        $userAuth     = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::EDIT)]);
        $roleEditable = $this->createEditableRole($userAuth->getNetwork());

        $editRole = [
            'name' => 'new role',
            'permissionsSlug' => $userAuth->role->stringPermissions()->pluck('slug')->toArray(),
            'networkSlug' => $userAuth->getNetwork()->slug
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->put(self::PREFIX . 'edit/' . $roleEditable->id, $editRole);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_permissions_user_should_response_200()
    {
        $userAuth = $this->createUserWithPermissions([RolePermission::getFullName(PermissionActions::EDIT)]);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userAuth))
            ->get('permissions/me');

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_return_200_with_file_roles()
    {
        $network = factory(Network::class)->create([
            'label' => 'NETWORK'
        ]);

        factory(Role::class)->create([
            'slug' => 'ROLE-SLUG',
            'name' => 'ROLE-NAME',
            'networkId' => $network->id
        ]);

        $header = "Rede;Funcao;Slug\nNETWORK;ROLE-NAME;ROLE-SLUG";

        $user = (new UserBuilder())->build();

        $response = $this->authAs($user)
            ->get('roles/export')
            ->assertStatus(Response::HTTP_OK);

        $this->assertContains($header, $response->content());
    }
}
