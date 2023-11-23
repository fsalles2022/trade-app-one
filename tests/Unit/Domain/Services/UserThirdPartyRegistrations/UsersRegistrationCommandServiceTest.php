<?php

namespace TradeAppOne\Tests\Unit\Domain\Services\UserThirdPartyRegistrations;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\UserThirdPartyRegistrations\RegistrationManagementService;
use TradeAppOne\Domain\Services\UserThirdPartyRegistrations\UsersRegistrationCommandService;
use TradeAppOne\Exceptions\SystemExceptions\UserRegistrationServiceNotFound;
use TradeAppOne\Tests\TestCase;

class UsersRegistrationCommandServiceTest extends TestCase
{
    /** @test */
    public function should_return_collection_when_operator_not_found()
    {
        $managementService = \Mockery::mock(RegistrationManagementService::class)->makePartial();
        $managementService->shouldReceive('syncPendingRegistrations')->never()->andReturn(collect());

        $this->expectException(UserRegistrationServiceNotFound::class);
        $commandService = new UsersRegistrationCommandService($managementService);
        $result         = $commandService->process(['operator' => 'aa']);

        self::assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_return_collection_when_operator_exists()
    {
        $managementService = \Mockery::mock(RegistrationManagementService::class)->makePartial();
        $managementService->shouldReceive('syncPendingRegistrations')->once()->andReturn(collect());

        $commandService = new UsersRegistrationCommandService($managementService);
        $result         = $commandService->process(['operator' => Operations::VIVO]);

        self::assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_return_collection_when_method_all()
    {
        $managementService = \Mockery::mock(RegistrationManagementService::class)->makePartial();
        $managementService->shouldReceive('syncAllSalesmenInTradeAppOne')->once()->andReturn(collect());

        $commandService = new UsersRegistrationCommandService($managementService);
        $result         = $commandService->process(['method' => 'all']);

        self::assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_return_collection_and_run_locally_when_method_empty()
    {
        $managementService = \Mockery::mock(RegistrationManagementService::class)->makePartial();
        $managementService->shouldReceive('syncPendingRegistrations')->once()->andReturn(collect());

        $commandService = new UsersRegistrationCommandService($managementService);
        $result         = $commandService->process();

        self::assertInstanceOf(Collection::class, $result);
    }
}
