<?php

namespace VivoBR\Tests\Unit\Services;

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\UserPendingRegistration;
use TradeAppOne\Domain\Services\Interfaces\UserThirdPartyRepository;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use VivoBR\Services\UserRegistrationVivoService;

class UserRegistrationVivoServiceTest extends TestCase
{
    /** @test */
    public function should_call_for_create_user_when_not_found_in_sun()
    {
        $network     = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $pointOfSale = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $user        = (new UserBuilder())->withNetwork($network)->build();
        factory(UserPendingRegistration::class)
            ->create([
                'userId'        => $user->id,
                'pointOfSaleId' => $user->pointsOfSale->first()->id,
            ]);
        $connectionMocked = \Mockery::mock(UserThirdPartyRepository::class)->makePartial();
        $connectionMocked->shouldReceive('findUser')
            ->andReturn([]);
        $connectionMocked->shouldReceive('createOrUpdate')
            ->once()
            ->andReturn([]);

        $service = new UserRegistrationVivoService($connectionMocked);

        self::assertEmpty($service->runOneInAPI($user, $pointOfSale));
    }

    /** @test */
    public function should_call_for_create_user_when_not_found_in_sun_and_flag()
    {
        $network     = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $pointOfSale = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $user        = (new UserBuilder())->withNetwork($network)->build();
        factory(UserPendingRegistration::class)
            ->create([
                'userId'        => $user->id,
                'pointOfSaleId' => $user->pointsOfSale->first()->id,
            ]);
        $connectionMocked = \Mockery::mock(UserThirdPartyRepository::class)->makePartial();
        $connectionMocked->shouldReceive('findUser')
            ->andReturn([]);
        $connectionMocked->shouldReceive('createOrUpdate')
            ->once()
            ->andReturn([UserThirdPartyRepository::CREATED, 1]);

        $service = new UserRegistrationVivoService($connectionMocked);
        $result  = $service->runOneInAPI($user, $pointOfSale);

        self::assertEquals(1, $result[1]);
    }

    /** @test */
    public function should_call_for_update_user_when_not_found_in_sun()
    {
        $network     = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $pointOfSale = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $user        = (new UserBuilder())->withNetwork($network)->build();
        factory(UserPendingRegistration::class)
            ->create([
                'userId'        => $user->id,
                'pointOfSaleId' => $user->pointsOfSale->first()->id,
            ]);
        $connectionMocked = \Mockery::mock(UserThirdPartyRepository::class)->makePartial();
        $connectionMocked->shouldReceive('findUser')
            ->andReturn([]);
        $connectionMocked->shouldReceive('createOrUpdate')
            ->once()
            ->andReturn([]);

        $service = new UserRegistrationVivoService($connectionMocked);
        $result  = $service->runOneInAPI($user, $pointOfSale);
    }

    /** @test */
    public function should_call_for_update_user_when_not_found_in_sun_and_flag()
    {
        $network     = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $pointOfSale = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $user        = (new UserBuilder())->withNetwork($network)->build();
        factory(UserPendingRegistration::class)
            ->create([
                'userId'        => $user->id,
                'pointOfSaleId' => $user->pointsOfSale->first()->id,
            ]);
        $connectionMocked = \Mockery::mock(UserThirdPartyRepository::class)->makePartial();
        $connectionMocked->shouldReceive('findUser')
            ->andReturn([]);
        $connectionMocked->shouldReceive('createOrUpdate')
            ->once()
            ->andReturn([UserThirdPartyRepository::CREATED, 1]);

        $service = new UserRegistrationVivoService($connectionMocked);
        $result  = $service->runOneInAPI($user, $pointOfSale);

        self::assertEquals([UserThirdPartyRepository::CREATED, 1], $result);
    }
}
