<?php

namespace TradeAppOne\Tests\Feature\User;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\UserPermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Facades\SyncUserOperators;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\EmailHelper;
use TradeAppOne\Tests\Helpers\UserHelper;
use TradeAppOne\Tests\TestCase;

class RegisterFeatureTest extends TestCase
{
    use EmailHelper, UserHelper;
    const CREATE = 'users/create/';

    /** @test */
    public function post_should_response_with_status_401_when_user_unauthorized()
    {
        $userManagement = (new UserBuilder())->build();

        $createUser = [
            'firstName'     => 'Marcia',
            'lastName'      => 'Pereira',
            'email'         => 'marcia@mail.com',
            'cpf'           => '12108961097',
            'areaCode'      => '28',
            'pointOfSale'   => $userManagement->pointsOfSale->first()->cnpj,
            'role'          => $userManagement->role->slug
        ];

        $token    = $this->loginUser($userManagement)['token'];
        $response = $this
            ->withHeader('Authorization', $token)
            ->post(self::CREATE, $createUser);

        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::UNAUTHORIZED)]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function post_should_response_with_status_422_when_cpf_is_invalid()
    {
        $user = factory(User::class)->states('invalid_cpf')->make()->toArray();

        $response = $this
            ->withHeader('Authorization', $this->generateTokenAuth())
            ->post(self::CREATE, $user);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_area_code_is_invalid()
    {
        $utils = $this->userWithPermissions();
        $user  = factory(User::class)->states('invalid_area_code')->make([
            'pointOfSale' => $utils['pointOfSale']->id,
            'pointOfSale' => $utils['user']->role->slug
        ])->toArray();

        $response = $this
            ->withHeader('Authorization', $this->generateTokenAuth())
            ->post(self::CREATE, $user);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_name_is_invalid()
    {
        $user = factory(User::class)->make()->toArray();
        unset($user['name']);

        $response = $this
            ->withHeader('Authorization', $this->generateTokenAuth())
            ->post(self::CREATE, $user);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_lastname_is_missing()
    {
        $user = factory(User::class)->make()->toArray();
        unset($user['lastName']);

        $response = $this
            ->withHeader('Authorization', $this->generateTokenAuth())
            ->post(self::CREATE, $user);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_email_is_missing()
    {
        $user = factory(User::class)->make()->toArray();
        unset($user['email']);

        $response = $this
            ->withHeader('Authorization', $this->generateTokenAuth())
            ->post(self::CREATE, $user);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_area_code_prefix_is_missing()
    {
        $user = factory(User::class)->make()->toArray();
        unset($user['areaCodePrefix']);

        $response = $this
            ->withHeader('Authorization', $this->generateTokenAuth())
            ->post(self::CREATE, $user);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_201_when_properties_are_valid()
    {
        $pointOfSale = factory(PointOfSale::class)->make();
        $network     = factory(Network::class)->create();
        $pointOfSale->network()->associate($network)->save();

        $userManagement = $this->createUserAuth();
        $userManagement->pointsOfSale()->attach($pointOfSale);

        $roleSalesman = factory(Role::class)->create([
            'level' => '100',
            'networkId' => $network->id
        ]);

        $salesman                = factory(User::class)->make()->toArray();
        $salesman['pointOfSale'] = $pointOfSale->cnpj;
        $salesman['role']        = "$roleSalesman->slug";

        $token    = $this->loginUser($userManagement)['token'];
        $response = $this
            ->withHeader('Authorization', $token)
            ->post(self::CREATE, $salesman);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function post_should_responde_with_status_422_when_already_persisted()
    {
        $utils       = $this->userWithPermissions();
        $role        = $utils['user']->role->slug;
        $pointOfSale = $utils['pointOfSale']->id;
        $user        = factory(User::class)->make([
            'cpf' => $utils['user']->cpf,
            'role' => $role,
            'pointOfSale' => $pointOfSale
        ])->toArray();

        $response = $this
            ->withHeader('Authorization', $this->generateTokenAuth())
            ->post(self::CREATE, $user);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_return_401_when_user_logged_not_have_pvd_user()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $userManagement = $this->createUserAuth();

        $salesman = factory(User::class)->make()->toArray();

        $salesman['pointOfSale'] = $pointOfSale->cnpj;
        $salesman['role']        = $userManagement->role->slug;

        $token = $this->loginUser($userManagement)['token'];

        $response = $this
            ->withHeader('Authorization', $token)
            ->post(self::CREATE, $salesman);

        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::NOT_BELONGS_TO_POINT_OF_SALE)]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function post_should_return_401_when_user_auth_not_permission_under_role()
    {
        $roleAdmin = (new RoleBuilder())->build();
        $roleUser  = (new RoleBuilder())->withParent($roleAdmin)->build();

        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug' => UserPermission::getFullName(PermissionActions::CREATE)
        ]);

        $userManagement = (new UserBuilder())->withPermissions([$permission])->withRole($roleUser)->build();

        $salesman = factory(User::class)->make()->toArray();

        $salesman['pointOfSale'] = $userManagement->pointsOfSale->first()->cnpj;
        $salesman['role']        = $roleAdmin->slug;

        $token = $this->loginUser($userManagement)['token'];

        $response = $this
            ->withHeader('Authorization', $token)
            ->post(self::CREATE, $salesman);

        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::NOT_PERMISSION_UNDER_ROLE, ['role' => $roleAdmin->slug])]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function put_should_response_200_when_verification_code_and_password_are_valid()
    {
        $this->mockEmail();

        $user = factory(User::class)->make()->toArray();
        $this->post(self::CREATE, $user);
        $verificationCode = DB::table('userVerifications')
            ->where('userId', $user['id'])
            ->first()['verificationCode'];
        $password         = ['password' => 'Test$1379033'];

        $response = $this->put("user/confirm/{$verificationCode}", $password);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function put_should_response_422_when_valid_verification_code_and_invalid_password_sent()
    {
        $user = factory(User::class)->states('add_points_of_sale')->make()->toArray();
        $this->post(self::CREATE, $user);
        $verificationCode = DB::table('userVerifications')
            ->where('userId', $user['id'])
            ->first()['verificationCode'];
        $password         = ['password' => '123456'];

        $response = $this->put("user/confirm/{$verificationCode}", $password);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_return_401_when_user_auth_not_authorization_under_hierarchy()
    {
        $hierarchy = (new HierarchyBuilder())->build();

        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug' => UserPermission::getFullName(PermissionActions::CREATE)
        ]);

        $userManagement = (new UserBuilder())->withPermissions([$permission])->build();

        $salesman                = factory(User::class)->make()->toArray();
        $salesman['pointOfSale'] = $userManagement->pointsOfSale->first()->cnpj;
        $salesman['role']        = $userManagement->role->slug;
        $salesman['hierarchy']   = $hierarchy->slug;

        $token = $this->loginUser($userManagement)['token'];

        $response = $this
            ->withHeader('Authorization', $token)
            ->post(self::CREATE, $salesman);

        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_HIERARCHY)]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function post_should_return_200_when_register_user_with_hierarchy()
    {
        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug' => UserPermission::getFullName(PermissionActions::CREATE)
        ]);

        $userManagement = (new UserBuilder())->withPermissions([$permission])->build();
        $hierarchy      = (new HierarchyBuilder())
            ->withUser($userManagement)
            ->withNetwork($userManagement->getNetwork())
            ->build();

        $salesman                = factory(User::class)->make()->toArray();
        $salesman['pointOfSale'] = $userManagement->pointsOfSale->first()->cnpj;
        $salesman['role']        = $userManagement->role->slug;
        $salesman['hierarchy']   = $hierarchy->slug;

        $token = $this->loginUser($userManagement)['token'];

        $response = $this
            ->withHeader('Authorization', $token)
            ->post(self::CREATE, $salesman);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->mockEmail();
        SyncUserOperators::shouldReceive('sync')->atLeast();
    }

    public function createUserAuth()
    {
        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug' => UserPermission::getFullName(PermissionActions::CREATE)
        ]);

        return (new UserBuilder())->withPermissions([$permission])->build();
    }

    public function generateTokenAuth()
    {
        $user = $this->createUserAuth();
        return $this->loginUser($user)['token'];
    }
}
