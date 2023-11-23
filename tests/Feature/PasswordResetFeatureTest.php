<?php

namespace TradeAppOne\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\ContextEnum;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\RecoveryPermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PasswordResetBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class PasswordResetFeatureTest extends TestCase
{
    use AuthHelper;

    protected $endpoint = '/password_recovery/';
    protected $tableName = 'passwordResets';

    /** @test */
    public function post_should_response_with_status_200_when_user_status_is_verified(): void
    {
        $user = (new UserBuilder())->build();
        $cpf  = ['cpf' => $user->cpf];

        $response = $this->json('POST', $this->endpoint, $cpf);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function post_should_response_with_status_200_when_user_status_is_active(): void
    {
        $user = (new UserBuilder())->build();
        $cpf  = ['cpf' => $user->cpf];

        $response = $this->json('POST', $this->endpoint, $cpf);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function post_should_response_with_422_when_user_status_is_inactive(): void
    {
        $user = (new UserBuilder())->withUserState('user_inactive')->build();
        $cpf  = ['cpf' => $user['cpf']];

        $this->json('POST', $this->endpoint, $cpf)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_404_when_cpf_not_exists(): void
    {
        $user = factory(User::class)->make()->toArray();
        $cpf  = ['cpf' => $user['cpf']];

        $response = $this->json('POST', $this->endpoint, $cpf);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function get_should_return_list_of_3_password_reset_request_when_user_has_CONTEXT_ALL(): void
    {
        $permissionContext = factory(Permission::class)->create([
            'client' => SubSystemEnum::API,
            'slug'   => RecoveryPermission::getFullName(ContextEnum::CONTEXT_ALL)
        ]);
        $network           = (new NetworkBuilder())->build();
        $userHasContextAll = (new UserBuilder())
            ->withNetwork($network)
            ->withPermissions([$permissionContext])
            ->build();

        (new HierarchyBuilder())->withUser($userHasContextAll)->build();

        (new PasswordResetBuilder())->WithDifferentNetworks()->generatePasswordResetTimes(3);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHasContextAll))
            ->json('GET', $this->endpoint);

        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function get_should_return_list_of_3_password_reset_request_when_user_has_CONTEXT_HIERARCHY(): void
    {

        $permissionHierarchy = factory(Permission::class)->create([
            'client' => 'API',
            'slug'   => RecoveryPermission::getFullName(ContextEnum::CONTEXT_HIERARCHY)
        ]);

        $permissionAll = factory(Permission::class)->create([
            'client' => 'API',
            'slug'   => RecoveryPermission::getFullName(ContextEnum::CONTEXT_ALL)
        ]);

        $network = (new NetworkBuilder())->build();

        $userHasContextAll = (new UserBuilder())->withNetwork($network)->withPermissions([$permissionAll])->build();
        $hierarchyParent   = (new HierarchyBuilder())->withUser($userHasContextAll)->build();

        $userHasContextHierarchy = (new UserBuilder())->withNetwork($network)->withPermissions([$permissionHierarchy])->build();
        $hierarchyChildren       = (new HierarchyBuilder())->withParent($hierarchyParent)->withUser($userHasContextHierarchy)->build();

        $pointOfSale           = $userHasContextHierarchy->pointsOfSale()->first();
        $userWhoForgotPassword = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->withRoleState('salesman')->build();
        (new PasswordResetBuilder())->withUser($userWhoForgotPassword)->build();

        $pointOfSaleTwo           = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchyChildren)->build();
        $userWhoForgotPasswordTwo = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSaleTwo)->withRoleState('salesman')->build();
        (new PasswordResetBuilder())->withUser($userWhoForgotPasswordTwo)->build();

        $pointOfSaleThree           = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchyChildren)->build();
        $userWhoForgotPasswordThree = (new UserBuilder())->withNetwork($network)->withRoleState('salesman')->withPointOfSale($pointOfSaleThree)->build();
        (new PasswordResetBuilder())->withUser($userWhoForgotPasswordThree)->build();

        (new PasswordResetBuilder())->WithDifferentNetworks()->generatePasswordResetTimes(5);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHasContextHierarchy))
            ->json('GET', $this->endpoint);

        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function post_should_response_with_status_200_when_manager_password_is_correct(): void
    {
        $permission = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => RecoveryPermission::getFullName(PermissionActions::APPROVE)
            ]);

        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $userManager = (new UserBuilder())
            ->withPermissions([$permission])
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->build();

        $userSalesman    = (new UserBuilder())
            ->withRoleState('salesman')
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->build();
        $userCPF         = $userSalesman->cpf;
        $managerPassword = '91910048';
        $response        = $this
            ->withHeader('Authorization', $this->loginUser($userManager))
            ->json('POST', "$this->endpoint?cpf=$userCPF&password=$managerPassword");

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_update_manager_id_when_manager_response_is_true(): void
    {
        $pointOfSale           = (new PointOfSaleBuilder())->build();
        $permission            = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug'   => RecoveryPermission::getFullName(PermissionActions::APPROVE)
        ]);
        $userManager           = (new UserBuilder())->withPointOfSale($pointOfSale)->withPermissions([$permission])->build();
        $userWhoForgotPassword = (new UserBuilder())->withPointOfSale($pointOfSale)->withRoleState('salesman')->build();
        (new PasswordResetBuilder())->withUser($userWhoForgotPassword)->build();
        $body = ['id' => $userWhoForgotPassword->id, 'response' => true];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userManager))
            ->put('password_recovery/', $body);
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas($this->tableName, ['managerId' => $userManager->id]);
    }

    /** @test */
    public function post_should_response_with_status_422_when_manager_password_is_incorrect(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $permission  = factory(Permission::class)->create([
            'client' => 'API',
            'slug'   => RecoveryPermission::getFullName(PermissionActions::APPROVE)
        ]);

        $userManager = (new UserBuilder())
            ->withNetwork($network)
            ->withPermissions([$permission])
            ->withPointOfSale($pointOfSale)
            ->build();

        $userSalesman      = (new UserBuilder())
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)->build();

      $this->withHeader('Authorization', $this->loginUser($userManager))
          ->json('POST', "$this->endpoint?cpf=$userSalesman->cpf&password=Incorrect_Password")
          ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function put_should_response_with_status_200_when_manager_response_is_true(): void
    {
        $permission  = factory(Permission::class)->create([
            'client' => SubSystemEnum::WEB,
            'slug'   => RecoveryPermission::getFullName(PermissionActions::APPROVE)
        ]);
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $userManager = (new UserBuilder())->withPointOfSale($pointOfSale)->withPermissions([$permission])->build();

        $userWhoForgotPassword = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        (new PasswordResetBuilder())->withUser($userWhoForgotPassword)->build();

        $body = ['id' => $userWhoForgotPassword->id, 'response' => true];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userManager))
            ->put('password_recovery/', $body);
        $response->assertStatus(Response::HTTP_OK);
    }
}
