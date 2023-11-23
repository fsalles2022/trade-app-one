<?php

namespace TradeAppOne\Tests\Feature\User;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\ContextEnum;
use TradeAppOne\Domain\Enumerators\Permissions\UserPermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ListUsersFeatureTest extends TestCase
{
    use AuthHelper;
    protected $endpointPrefix = '/users/list';

    /** @test */
    public function get_should_return_list_of_3_users_when_user_has_CONTEXT_ALL(): void
    {
        $network           = (new NetworkBuilder())->build();
        $userHasContextAll = (new UserBuilder())->withNetwork($network)->build();
        (new HierarchyBuilder())->withUser($userHasContextAll)->build();

        (new UserBuilder())->generateUserTimes(2);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHasContextAll))
            ->json('POST', $this->endpointPrefix);

        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function get_should_return_list_of_3_users_when_user_has_CONTEXT_HIERARCHY(): void
    {
        $network = (new NetworkBuilder())->build();

        $permissionHierarchy = factory(Permission::class)->create([
            'client' => SubSystemEnum::API,
            'slug'   => UserPermission::getFullName(ContextEnum::CONTEXT_HIERARCHY)
        ]);

        $permissionAll = factory(Permission::class)->create([
            'client' => SubSystemEnum::API,
            'slug'   => UserPermission::getFullName(ContextEnum::CONTEXT_ALL)
        ]);

        $userHasContextAll = (new UserBuilder())->withPermissions([$permissionAll])->withNetwork($network)->build();
        $hierarchyParent   = (new HierarchyBuilder())->withUser($userHasContextAll)->build();

        $userHasContextHierarchy = (new UserBuilder())->withNetwork($network)->withPermissions([$permissionHierarchy])->build();
        $hierarchyChildren       = (new HierarchyBuilder())->withParent($hierarchyParent)->withUser($userHasContextHierarchy)->build();

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchyChildren)->build();
        (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->withRoleState('salesman')->build();

        $pointOfSaleTwo = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchyChildren)->build();
        (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSaleTwo)->withRoleState('salesman')->build();

        (new UserBuilder())->withRoleState('salesman')->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHasContextHierarchy))
            ->json('POST', $this->endpointPrefix);
        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function get_should_return_users_who_not_have_pointOfSale_has_only_hierarchy(): void
    {
        $user      = (new UserBuilder())->build();
        $hierarchy = (new HierarchyBuilder())->withUser($user)->build();

        $userHelp = (new UserBuilder())->build();
        $userHelp->pointsOfSale()->detach();
        $userHelp->hierarchies()->attach($hierarchy);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post($this->endpointPrefix);

        $response->assertJsonFragment(['total' => 2]);
    }

    /** @test */
    public function get_should_return_users_belonging_roles_hierarchies(): void
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $roleAdmin  = (new RoleBuilder())->build();
        $roleUser   = (new RoleBuilder())->withParent($roleAdmin)->build();
        $roleHelper = (new RoleBuilder())->withParent($roleUser)->build();

        $user       = (new UserBuilder())->withPointOfSale($pointOfSale)->withRole($roleUser)->build();

        (new UserBuilder())->withPointOfSale($pointOfSale)->withRole($roleHelper)->build();
        (new UserBuilder())->withPointOfSale($pointOfSale)->withRole($roleHelper)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post($this->endpointPrefix);

        $response->assertJsonFragment(['total' => 2]);
    }

    /** @test */
    public function should_list_points_of_sale_that_user_authenticated_has_authorized(): void
    {
        $network     = (new NetworkBuilder())->build();
        $hierarchies = (new HierarchyBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withHierarchy($hierarchies)->withNetwork($network)->build();

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchies)->build();

        (new UserBuilder())->withPointOfSale($pointOfSale)->withNetwork($network)->build();

        $service = factory(Service::class)->create([
            'sector'    => 'LINE_ACTIVATION',
            'operator'  => 'CLARO',
            'operation' => 'CONTROLE_BOLETO'
        ]);

        $availableService = factory(AvailableService::class)->create([
            'serviceId' => $service->id,
            'pointOfSaleId' => $pointOfSale->id,
            'networkId' => $network->id
        ]);

        $cart = factory(ServiceOption::class)->create([
            'action' => ServiceOption::CARTEIRIZACAO
        ]);

        $availableService->options()->sync($cart);

        $this->withHeader('Authorization', $this->loginUser($user))
            ->get('/users/list/by-points-of-sale')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['cnpj' => $pointOfSale->cnpj])
            ->assertJsonStructure([
                '0' => [
                    'users' => []
                ]
            ]);
    }
}
