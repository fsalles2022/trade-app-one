<?php

namespace TradeAppOne\Tests\Unit\Domain\Services;

use TradeAppOne\Domain\Services\ServiceService;
use TradeAppOne\Tests\TestCase;

class ServiceServiceTest extends TestCase
{

    /** @test */
    public function test_update_available_services_should_work()
    {
        $serviceServiceMock = $this->createMock(ServiceService::class);

        $serviceServiceMock->method('updateAvailableServices')
            ->with(['foo' => 'bar'])
            ->willReturn(true);

        $this->assertEquals(
            true, 
            $serviceServiceMock->updateAvailableServices(['foo' => 'bar'])
        );
    }
}
