<?php

namespace VivoBR\Tests\Unit\Console\Commands;

use TradeAppOne\Domain\Services\Interfaces\UserThirdPartyRepository;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use VivoBR\Services\UserRegistrationVivoService;

class VivoUserRegistrationServiceTest extends TestCase
{
    /** @test */
    public function should_call_api_and_return_array()
    {
        $user       = (new UserBuilder())->build();
        $repository = \Mockery::mock(UserThirdPartyRepository::class)->makePartial();
        $repository->shouldReceive('createOrUpdate')->once()->andReturn([]);

        $registration = new UserRegistrationVivoService($repository);

        $result = $registration->runOneInAPI($user, $user->pointsOfSale()->first());
        self::assertTrue(is_array($result));
    }

    /** @test */
    public function should_call_api_and_return_false_when_user_not_found()
    {
        $user       = (new UserBuilder())->build();
        $repository = \Mockery::mock(UserThirdPartyRepository::class)->makePartial();
        $repository->shouldReceive('findUser')->once()->andReturn([]);

        $registration = new UserRegistrationVivoService($repository);

        $result = $registration->isSyncedInAPI($user);
        self::assertFalse($result);
    }

    /** @test */
    public function should_call_api_and_return_false_when_user_not_synced()
    {
        $user       = (new UserBuilder())->build();
        $repository = \Mockery::mock(UserThirdPartyRepository::class)->makePartial();
        $repository->shouldReceive('findUser')->once()->andReturn([
            'nome' => 'Teste',
            'cnpj' => [
                '2222222222'
            ]
        ]);

        $registration = new UserRegistrationVivoService($repository);

        $result = $registration->isSyncedInAPI($user);
        self::assertFalse($result);
    }

    /** @test */
    public function should_call_api_and_return_false_when_user_is_synced()
    {
        $user       = (new UserBuilder())->build();
        $repository = \Mockery::mock(UserThirdPartyRepository::class)->makePartial();
        $repository->shouldReceive('findUser')->once()->andReturn([
            'nome' => 'Teste',
            'cnpj' => [
                $user->pointsOfSale->first()->cnpj
            ]
        ]);

        $registration = new UserRegistrationVivoService($repository);

        $result = $registration->isSyncedInAPI($user);
        self::assertTrue($result);
    }

    /** @test */
    public function should_call_api_and_return_false_when_user_is_synced_and_has_many()
    {
        $user       = (new UserBuilder())->build();
        $repository = \Mockery::mock(UserThirdPartyRepository::class)->makePartial();
        $repository->shouldReceive('findUser')->once()->andReturn([
            'nome' => 'Teste',
            'cnpj' => [
                $user->pointsOfSale->first()->cnpj, '11111111111'
            ]
        ]);

        $registration = new UserRegistrationVivoService($repository);

        $result = $registration->isSyncedInAPI($user);
        self::assertTrue($result);
    }
}
