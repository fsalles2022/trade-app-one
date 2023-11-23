<?php

namespace TradeAppOne\Tests\Unit\Domain\Repositories;


use TradeAppOne\Domain\Enumerators\RoleEnum;
use TradeAppOne\Domain\Repositories\Collections\RoleRepository;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class RoleRepositoryTest extends TestCase
{

    /** @test */
    public function should_return_all_roles_when_parent_is_null()
    {
        $repository = new RoleRepository();
        $user = (new UserBuilder())->build();
        (new RoleBuilder())->withParent($user->role)->build();
        (new RoleBuilder())->build();

        $received = $repository->getRolesThatUserHasAuthority($user);

        $this->assertCount(4, $received);

    }

    /** @test */
    public function should_return_the_roles_brother_parent()
    {
        $repository = new RoleRepository();

        $roleSupAdm  = (new RoleBuilder())->build();
        $roleAdm     = (new RoleBuilder())->withParent($roleSupAdm)->build();
        $roleSuporte = (new RoleBuilder())->withParent($roleAdm)->build();
        $roleSuporte->update(['slug' => RoleEnum::SUPPORT_TRADEUP[0]]);

        $roleOtherNetwork = (new RoleBuilder())->withParent($roleAdm)->build();

        $user = (new UserBuilder())->withRole($roleSuporte)->build();

        $received = $repository->getRolesThatUserHasAuthority($user);

        $this->assertCount(2, $received);
    }

}