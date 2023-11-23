<?php

namespace TradeAppOne\Tests\Unit\Policies;

use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class PermissionsPoliciesTest extends TestCase
{
    /** @test */
    function should_return_false_when_user_not_permission()
    {
        $user = (new UserBuilder())->build();
        $this->actingAs($user);

        $result = hasPermission('');
        $this->assertFalse($result);
    }

    /** @test */
    function should_return_false_when_user_is_logged()
    {
        $result = hasPermission('');
        self::assertFalse($result);
    }

    /** @test */
    function should_return_true_when_user_has_permission()
    {
        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::API,
            'slug'   => 'update'
        ]);
        $user = (new UserBuilder())->withPermissions([$permission])->build();
        $this->actingAs($user);

        $result = hasPermission('update');
        $this->assertTrue($result);
    }

    /** @test */
    function should_abort_when_user_not_has_permission()
    {
        $user = (new UserBuilder())->build();
        $this->actingAs($user);

        $this->expectExceptionMessage(trans('exceptions.user.' . UserExceptions::UNAUTHORIZED));
        hasPermissionOrAbort('');
    }

    /** @test */
    function should_return_true_when_user_has_permission_per_function_hasPermissionOrAbort()
    {
        $permission = factory(Permission::class)->create([
            'client' => SubSystemEnum::API,
            'slug'   => 'update'
        ]);
        $user = (new UserBuilder())->withPermissions([$permission])->build();
        $this->actingAs($user);

        $result = hasPermissionOrAbort('update');
        $this->assertTrue($result);
    }
}
