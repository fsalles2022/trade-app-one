<?php

namespace TradeAppOne\Tests\Helpers\Traits;

use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

trait CustomUsersRole
{
    public function createUserWithPermissions(array $slugPermissions = ["permission-fake"])
    {
        $network = $this->createNetworks();

        $role = (new RoleBuilder())->withNetwork($network)->build();

        $user = (new UserBuilder())
            ->withNetwork($network)
            ->withRole($role)
            ->build();

        $this->associatePermissions($user, $slugPermissions);
        return $user;
    }

    public function createEditableRole(Network $network)
    {
        return (new RoleBuilder())->withNetwork($network)->build();
    }

    public function associatePermissions(User $user, array $slugPermissions)
    {
        foreach ($slugPermissions as $slugPermission) {
            $permission = factory(Permission::class)->states('web')->create([
                'slug' => $slugPermission
            ]);

            $user->role->stringPermissions()->attach($permission);
        }

        return $user;
    }

    public function createNetworks()
    {
        $network = factory(Network::class)->create([
            'slug' => 'tradeup'
        ]);

        factory(Network::class)->create([
            'slug' => 'tradeup-group'
        ]);

        return $network;
    }

}