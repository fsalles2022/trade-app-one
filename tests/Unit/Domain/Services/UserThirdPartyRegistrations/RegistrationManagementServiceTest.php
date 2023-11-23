<?php

namespace TradeAppOne\Tests\Unit\Domain\Services\UserThirdPartyRegistrations;

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\UserPendingRegistration;
use TradeAppOne\Domain\Services\UserThirdPartyRegistrations\RegistrationManagementService;
use TradeAppOne\Domain\Services\UserThirdPartyRegistrations\UserRegistrationService;
use TradeAppOne\Domain\Services\UserThirdPartyRegistrations\UsersRegistrationCommandService;
use TradeAppOne\Domain\Services\UserThirdPartyRegistrationService;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class RegistrationManagementServiceTest extends TestCase
{
    /** @test */
    public function should_sync_one()
    {
        $assert              = array('action' => 'UPDATED', 'status' => true);
        $network             = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $pointOfSale         = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $pointOfSale1        = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $user                = (new UserBuilder())
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->build();
        $pendingRegistration = factory(UserPendingRegistration::class)
            ->create([
                'userId'        => $user->id,
                'pointOfSaleId' => $pointOfSale->id
            ]);

        $registrationService = \Mockery::mock(UserRegistrationService::class)->makePartial();
        $registrationService->shouldReceive('runOneInAPI')->andReturn(array('UPDATED', true));
        $registrationService->shouldReceive('getOperator')->andReturn('');

        $management = new RegistrationManagementService();
        $return     = $management->syncOneInLocal($user->cpf, $registrationService);
        self::assertEquals($assert, $return->first());
    }

    /** @test */
    public function should_sync_one_filled_when_exist_pending_registration()
    {
        $assert              = array('UPDATED', true);
        $network             = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $pointOfSale         = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $pointOfSale1        = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $user                = (new UserBuilder())
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->build();
        $pendingRegistration = factory(UserPendingRegistration::class)
            ->create([
                'userId'        => $user->id,
                'pointOfSaleId' => $pointOfSale1->id,
                'operator'      => Operations::VIVO,
                'done'          => false
            ]);

        $registrationService     = \Mockery::mock(UserRegistrationService::class)->makePartial();
        $baseRegistrationService = \Mockery::mock(UserThirdPartyRegistrationService::class)->makePartial();

        $registrationService->shouldReceive('runOneInAPI')->once()->andReturn($assert);
        $registrationService->shouldReceive('getOperator')->twice()->andReturn(Operations::VIVO);
        $baseRegistrationService->shouldReceive('create')->never();

        $management = new RegistrationManagementService();
        $result     = $management->syncOneInLocal($user->cpf, $registrationService);
        self::assertNotEmpty($result->first());
    }

    /** @test */
    public function should_dont_sync_one_when_point_of_sale_not_changed()
    {
        $assert              = array('UPDATED', true);
        $network             = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $pointOfSale         = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $user                = (new UserBuilder())
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->build();
        $pendingRegistration = factory(UserPendingRegistration::class)
            ->create([
                'userId'        => $user->id,
                'pointOfSaleId' => $pointOfSale->id,
                'operator'      => Operations::VIVO,
                'done'          => false
            ]);

        $registrationService     = \Mockery::mock(UserRegistrationService::class)->makePartial();
        $baseRegistrationService = \Mockery::mock(UserThirdPartyRegistrationService::class)->makePartial();

        $registrationService->shouldReceive('runOneInAPI')->once()->andReturn($assert);
        $registrationService->shouldReceive('getOperator')->once()->andReturn(Operations::VIVO);
        $baseRegistrationService->shouldReceive('create')->never();

        $management = new RegistrationManagementService();
        $result     = $management->syncOneInLocal($user->cpf, $registrationService);
        self::assertNotEmpty($result->first());
    }

    /** @test */
    public function should_dont_sync_one__and_update_status_when_point_of_sale_not_changed()
    {
        $assert              = array('action' => 'UPDATED', 'status' => true);
        $network             = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $pointOfSale         = factory(PointOfSale::class)->create(['networkId' => $network->id]);
        $user                = (new UserBuilder())
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->build();
        $pendingRegistration = factory(UserPendingRegistration::class)
            ->create([
                'userId'        => $user->id,
                'pointOfSaleId' => $pointOfSale->id,
                'operator'      => Operations::VIVO,
                'done'          => false
            ]);

        $registrationService     = \Mockery::mock(UserRegistrationService::class)->makePartial();
        $baseRegistrationService = \Mockery::mock(UserThirdPartyRegistrationService::class)->makePartial();

        $registrationService->shouldReceive('runOneInAPI')->once()->andReturn(array('UPDATED', true));
        $registrationService->shouldReceive('getOperator')->once()->andReturn(Operations::VIVO);
        $baseRegistrationService->shouldReceive('create')->never();

        $management = new RegistrationManagementService();
        $result     = $management->syncOneInLocal($user->cpf, $registrationService);
        self::assertEquals($assert, $result->first());
    }

    /** @test */
    public function should_execute_one_assistance_when_operator_sent()
    {
        $management = \Mockery::mock(RegistrationManagementService::class)->makePartial();
        $management->shouldReceive('syncAllSalesmenInTradeAppOne')->never();
        $management->shouldReceive('syncOneInLocal')->never();
        $management->shouldReceive('syncPendingRegistrations')->once()->andReturn(collect());

        $options = ['operator' => Operations::VIVO, 'method' => 'local'];
        $command = new UsersRegistrationCommandService($management);

        $command->process($options);
    }

    /** @test */
    public function should_execute_one_assistance_when_operator_and_all_sent()
    {
        $management = \Mockery::mock(RegistrationManagementService::class)->makePartial();
        $management->shouldReceive('syncPendingRegistrations')->never();
        $management->shouldReceive('syncOneInLocal')->never();
        $management->shouldReceive('syncAllSalesmenInTradeAppOne')->andReturn(collect());

        $options = ['operator' => Operations::VIVO, 'method' => 'all'];
        $command = new UsersRegistrationCommandService($management);

        $command->process($options);
    }

    /** @test */
    public function should_execute_one_assistance_when_operator_and_user_sent()
    {
        $management = \Mockery::mock(RegistrationManagementService::class)->makePartial();
        $management->shouldReceive('syncPendingRegistrations')->never();
        $management->shouldReceive('syncAllSalesmenInTradeAppOne')->never();
        $management->shouldReceive('syncOneInLocal')->once();

        $options = ['operator' => Operations::VIVO, 'user' => '0000000'];
        $command = new UsersRegistrationCommandService($management);

        $command->process($options);
    }
}
