<?php

namespace TradeAppOne\Tests\Feature\User;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\UserPermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Exportables\UserExport;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Facades\SyncUserOperators;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\UserHelper;
use TradeAppOne\Tests\TestCase;

class UserFeatureTest extends TestCase
{
    use UserHelper;

    protected $endpointPrefix = '/users/';
    const EDIT                = '/users/edit/';
    const SHOW                = '/users/show/';

    public function setUp()
    {
        parent::setUp();
        SyncUserOperators::shouldReceive('sync')->atLeast();
    }

    /** @test */
    public function should_return_200()
    {
        $response = $this
            ->withHeader('Authorization', $this->userWithPermissions()['token'])
            ->json('POST', $this->endpointPrefix . 'export');
        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_return_csv()
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $user->pointsOfSale->first();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user)['token'])
            ->post($this->endpointPrefix . 'export');

        $export = (new UserExport())->getCsv();

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($export);
        $response->assertSee($user->cpf);
        $response->assertSee($user->firstName);
        $response->assertSee($user->lastName);
        $response->assertSee(trans('constants.user.status.' . $user->activationStatusCode));
        $response->assertSee($user->role->slug);
        $response->assertSee($user->role->name);
        $response->assertSee($pointOfSale->cnpj);
        $response->assertSee($pointOfSale->slug);
    }

    /** @test */
    public function put_should_return_401_when_user_not_permission_edit_users()
    {
        $userManagement = (new UserBuilder())->build();
        $userEdit       = (new UserBuilder())->build();

        $token = $this->loginUser($userManagement)['token'];

        $edit = [
            'firstName'     => 'Marcia',
            'lastName'      => 'Pereira',
            'email'         => 'marcia@mail.com',
            'areaCode'      => '28',
            'pointOfSale'   => $userManagement->pointsOfSale->first()->cnpj,
            'role'          => $userManagement->role->slug
        ];

        $response = $this
            ->withHeader('Authorization', $token)
            ->put(self::EDIT.$userEdit->cpf, $edit);

        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::UNAUTHORIZED)]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function put_should_return_200_when_user_edited()
    {
        $userManagement = $this->createUserPermissionEdit();

        $userEdit = (new UserBuilder())->build();

        $token = $this->loginUser($userManagement)['token'];

        $edit = [
            'firstName'     => 'Marcia',
            'lastName'      => 'Pereira',
            'email'         => 'marcia@mail.com',
            'areaCode'      => '28',
            'pointOfSale'   => $userManagement->pointsOfSale->first()->cnpj,
            'role'          => $userManagement->role->slug
        ];

        $response = $this
            ->withHeader('Authorization', $token)
            ->put(self::EDIT.$userEdit['cpf'], $edit);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function put_should_return_404_when_cpf_not_exists_in_edit()
    {
        $userManagement = $this->createUserPermissionEdit();

        $token   = $this->loginUser($userManagement)['token'];
        $fakeCPF = '55234396061';

        $edit = [
            'firstName'     => 'Marcia',
            'lastName'      => 'Pereira',
            'email'         => 'marcia@mail.com',
            'areaCode'      => '28',
            'pointOfSale'   => $userManagement->pointsOfSale->first()->cnpj,
            'role'          => $userManagement->role->slug
        ];

        $response = $this
            ->withHeader('Authorization', $token)
            ->put(self::EDIT.$fakeCPF, $edit);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function put_should_return_401_when_user_auth_has_not_authorization_under_user_edited()
    {
        $network = factory(Network::class)->create();

        $roleAdmin  = (new RoleBuilder())->withNetwork($network)->build();
        $roleDefine = (new RoleBuilder())->withParent($roleAdmin)->withNetwork($network)->build();
        $roleEdited = (new RoleBuilder())->withParent($roleDefine)->withNetwork($network)->build();

        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug'   => UserPermission::getFullName(PermissionActions::EDIT)
        ]);

        $userManagement = (new UserBuilder())->withRole($roleDefine)->withPermissions([$permission])->withNetwork($network)->build();

        $userEdited = (new UserBuilder())->withRole($roleAdmin)->withNetwork($network)->build();

        $token = $this->loginUser($userManagement)['token'];

        $edit = [
            'firstName'     => 'Marcia',
            'lastName'      => 'Pereira',
            'email'         => 'marcia@mail.com',
            'areaCode'      => '28',
            'pointOfSale'   => $userManagement->pointsOfSale->first()->cnpj,
            'role'          => $roleEdited->slug
        ];

        $response = $this
            ->withHeader('Authorization', $token)
            ->put(self::EDIT.$userEdited->cpf, $edit);

        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_USER)]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function get_should_return_user_and_pdv_when_search_by_cpf_show()
    {
        $network    = (new NetworkBuilder())->build();
        $userLogged = (new UserBuilder())->withNetwork($network)->build();

        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug'   => UserPermission::getFullName(PermissionActions::CREATE)
        ]);

        $pointOfSale = $userLogged->pointsOfSale()->first();
        $userToView  = (new UserBuilder())->withPointOfSale($pointOfSale)->withPermissions([$permission])->build();

        $token    = $this->loginUser($userLogged)['token'];
        $response = $this
            ->withHeader('Authorization', $token)
            ->get(self::SHOW.$userToView->cpf);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'pointsOfSale' => true,
                'role' => true
            ]);
    }

    /** @test */
    public function get_should_return_403_when_user_not_belong_same_point_of_sale_show()
    {
        $network    = (new NetworkBuilder())->build();
        $userLogged = (new UserBuilder())->withNetwork($network)->build();

        $userToView = (new UserBuilder())->build();

        $token    = $this->loginUser($userLogged)['token'];
        $response = $this
            ->withHeader('Authorization', $token)
            ->get(self::SHOW.$userToView->cpf);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertEquals(
            $response->json('message'),
            trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_USER)
        );
    }

    /** @test */
    public function get_should_return_404_when_user_not_found_in_method_show()
    {
        $cpfFake = '25332595867';

        $response = $this
            ->withHeader('Authorization', $this->generateTokenWithPermissionEdit())
            ->get(self::SHOW.$cpfFake);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function createUserFake()
    {
        $role = factory(Role::class)->make([
            'level' => '1000'
        ]);

         return  ((new UserBuilder())->withRole($role)->build())->toArray();
    }

    public function createUserPermissionEdit()
    {
        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug'   => UserPermission::getFullName(PermissionActions::EDIT)
        ]);

        return (new UserBuilder())->withPermissions([$permission])->build();
    }

    public function generateTokenWithPermissionEdit()
    {
        $user = $this->createUserPermissionEdit();
        return $this->loginUser($user)['token'];
    }
}
