<?php

namespace TradeAppOne\Tests\Helpers;

use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Models\Tables\UserAuthAlternates;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use Tymon\JWTAuth\Facades\JWTAuth;

trait UserHelper
{
    public function userSalesman()
    {
        $networkEntity      = factory(Network::class)->create();
        $pointOfSaleFactory = factory(PointOfSale::class)->make();
        $roleFactory        = factory(Role::class)->states('salesman')->make();
        $userFactory        = factory(User::class)->states('user_active', 'another_password')->make();

        $userEntity = $this->associateUserRelations($networkEntity, $pointOfSaleFactory, $roleFactory, $userFactory);

        return $this->loginUser($userEntity);
    }

    public function userWithSameNetwork(Network $networkEntity, string $userType)
    {
        $pointOfSaleFactory = factory(PointOfSale::class)->make();
        $roleFactory        = factory(Role::class)->states($userType)->make();
        $userFactory        = factory(User::class)->states('user_active')->make();

        $userEntity = $this->associateUserRelations($networkEntity, $pointOfSaleFactory, $roleFactory, $userFactory);

        return $this->loginUser($userEntity);
    }

    public function userWithSameNetworkAndPointOfSale(Network $network, PointOfSale $pointOfSale, string $userType)
    {
        $roleFactory = factory(Role::class)->states($userType)->make();
        $userFactory = factory(User::class)->states('user_active')->make();

        $userEntity = $this->associateUserRelations($network, $pointOfSale, $roleFactory, $userFactory);

        return $this->loginUser($userEntity);
    }

    public function userWithPermissions()
    {
        $pointOfSale = PointOfSaleBuilder::make()->build();
        $network     = NetworkBuilder::make()
            ->withRandomServices()
            ->build();
        $role        = RoleBuilder::make()
            ->withRoleState('admin')
            ->build();

        $user        = UserBuilder::make()
            ->withUserState('user_active')
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->withRole($role)
            ->withPermission(factory(Permission::class)->create())
            ->build();

        return $this->loginUser($user);
    }

    public function userWithAltenativeAuthAndPermissions(): array
    {
        $pointOfSale = PointOfSaleBuilder::make()->build();
        $network     = NetworkBuilder::make()
            ->withRandomServices()
            ->build();
        $role        = RoleBuilder::make()
            ->withRoleState('admin')
            ->build();

        $user        = UserBuilder::make()
            ->withUserState('user_active')
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->withRole($role)
            ->withPermission(factory(Permission::class)->create())
            ->build();

        factory(UserAuthAlternates::class)->create(['userId' => $user->id]);

        return $this->loginUser($user);
    }

    public function userWithScopeOwnNetwork()
    {
        $networkEntity      = factory(Network::class)->create();
        $pointOfSaleFactory = factory(PointOfSale::class)->make();
        $roleFactory        = factory(Role::class)->states('scope_own_network')->make();
        $userFactory        = factory(User::class)->states('user_active')->make();

        $userEntity = $this->associateUserRelations($networkEntity, $pointOfSaleFactory, $roleFactory, $userFactory);

        return $this->loginUser($userEntity);
    }

    public function userWithScopeOwnPointOfSale()
    {
        $networkEntity      = factory(Network::class)->create();
        $pointOfSaleFactory = factory(PointOfSale::class)->make();
        $roleFactory        = factory(Role::class)->states('scope_own_point_of_sale')->make();
        $userFactory        = factory(User::class)->states('user_active')->make();

        $userEntity = $this->associateUserRelations($networkEntity, $pointOfSaleFactory, $roleFactory, $userFactory);

        return $this->loginUser($userEntity);
    }

    public function userInactive()
    {
        $networkEntity      = factory(Network::class)->create();
        $pointOfSaleFactory = factory(PointOfSale::class)->make();
        $userFactory        = factory(User::class)->states('user_inactive')->make();
        $roleFactory        = factory(Role::class)->states('admin')->make();

        $userEntity = $this->associateUserRelations($networkEntity, $pointOfSaleFactory, $roleFactory, $userFactory);

        return $this->loginUser($userEntity);
    }

    public function associateUserRelations(Network $networkEntity, PointOfSale $pointOfSaleFactory, Role $roleFactory, User $userFactory, Permission $permissionFactory = null): User
    {
        $pointOfSaleFactory->network()->associate($networkEntity)->save();
        $roleFactory->network()->associate($networkEntity)->save();
        $userFactory->role()->associate($roleFactory)->save();

        $userFactory->pointsOfSale()->attach($pointOfSaleFactory);
        if ($permissionFactory) {
            $roleFactory->stringPermissions()->attach($permissionFactory);
        }

        return $userFactory;
    }

    public function loginUser(User $user): array
    {
        $credentials             = ['cpf' => $user->cpf, 'password' => '91910048'];
        $token                   = JWTAuth::attempt($credentials);
        $token                   = "Bearer {$token}";
        $resource['user']        = $user;
        $resource['token']       = $token;
        $resource['role']        = $user->role;
        $resource['pointOfSale'] = $user->pointsOfSale->first();
        return $resource;
    }
}
