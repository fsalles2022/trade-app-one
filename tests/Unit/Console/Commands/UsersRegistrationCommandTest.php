<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Mockery;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\UserThirdPartyRegistrations\RegistrationManagementService;
use TradeAppOne\Exceptions\SystemExceptions\UserRegistrationServiceNotFound;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class UsersRegistrationCommandTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $mock = Mockery::mock(RegistrationManagementService::class)->makePartial();
        $mock
            ->shouldReceive(
                'syncOneInLocal',
                'syncAllSalesmenInTradeAppOne',
                'syncPendingRegistrations'
            )
            ->withAnyArgs()
            ->andReturn(collect([['status' => false]]));
        $this->app->singleton(RegistrationManagementService::class, function () use ($mock) {
            return $mock;
        });
    }

    /** @test */
    public function should_allow_operator_and_user_as_parameter()
    {
        $user = (new UserBuilder())->build();
        Artisan::call('user-registration', [
            '--operator' => Operations::VIVO,
            '--user' => $user->cpf
        ]);
    }

    /** @test */
    public function should_allow_only_operator_as_parameter()
    {
        Artisan::call('user-registration', [
            '--operator' => Operations::VIVO,
        ]);
    }

    /** @test */
    public function should_allow_only_user_as_parameter()
    {
        $user = (new UserBuilder())->build();
        Artisan::call('user-registration', [
            '--user' => $user->cpf
        ]);
    }

    /** @test */
    public function should_allow_operator_and_method_as_parameter()
    {
        Artisan::call('user-registration', [
            '--operator' => Operations::VIVO,
            '--method' => 'all'
        ]);
    }

    /** @test */
    public function should_allow_only_method_as_parameter()
    {
        Artisan::call('user-registration', [
            '--method' => 'all'
        ]);
    }

    /** @test */
    public function should_return_an_exception_when_operator_is_invalid()
    {
        $this->expectException(UserRegistrationServiceNotFound::class);
        Artisan::call('user-registration', [
            '--operator' => 'invalidOperator'
        ]);
    }
}
