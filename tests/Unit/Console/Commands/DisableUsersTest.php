<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use TradeAppOne\Console\Commands\DisableUsers;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class DisableUsersTest extends TestCase
{
    const COMMAND = 'users:disable';

    /** @test */
    public function should_command_exists_in_artisan()
    {
        $this->assertTrue(array_has(Artisan::all(), self::COMMAND));
    }

    /** @test */
    public function should_inactivate_users_that_not_logged_specified_days_to_inactive()
    {
        $user = (new UserBuilder())->build();

        $user->lastSignin = Carbon::now()->subDays(DisableUsers::DAYS_TO_INACTIVE)->toDateTimeString();
        $user->save();

        Artisan::call(self::COMMAND);
        $userChanged = User::find($user->id);

        $this->assertEquals(UserStatus::INACTIVE, $userChanged->activationStatusCode);
    }

    /** @test */
    public function should_soft_delete_users_that_not_logged_specified_days_to_delete()
    {
        $user = (new UserBuilder())->withUserState('user_inactive')->build();

        $user->lastSignin = Carbon::now()->subDays(DisableUsers::DAYS_TO_SOFT_DELETE)->toDateTimeString();
        $user->save();

        Artisan::call(self::COMMAND);
        $userChanged = User::withTrashed()->find($user->id);

        $this->assertNotNull($userChanged->deletedAt);
    }
}
