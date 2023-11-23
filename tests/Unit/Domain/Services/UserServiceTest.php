<?php

namespace TradeAppOne\Tests\Unit\Domain\Services;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\ContextEnum;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Repositories\Collections\HierarchyRepository;
use TradeAppOne\Domain\Repositories\Collections\UserRepository;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Notifications\NotifyUser;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\UserHelper;
use TradeAppOne\Tests\TestCase;

class UserServiceTest extends TestCase
{
    use UserHelper;

    /** @test */
    public function should_return_an_instance()
    {
        $userService = resolve(UserService::class);
        $className   = get_class($userService);
        $this->assertEquals(UserService::class, $className);
    }

    /** @test */
    public function should_return_context_non_existent_whe_user_logged_hasnt_context()
    {
        $userService = resolve(UserService::class);
        $this->userWithPermissions()['user'];
        $context = $userService->getUserContext(SubSystemEnum::API, 'CAOS');

        $this->assertEquals(ContextEnum::CONTEXT_NON_EXISTENT, $context);
    }

    /** @test */
    public function should_return_valid_context()
    {
        $permission     = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(ContextEnum::CONTEXT_ALL)
            ]);

        $userService = resolve(UserService::class);

        $user = (new UserBuilder())->withPermissions([$permission])->build();
        Auth::setUser($user);
        $context = $userService->getUserContext(SubSystemEnum::API, SalePermission::NAME);

        $this->assertEquals(ContextEnum::CONTEXT_ALL, $context);
    }
}
