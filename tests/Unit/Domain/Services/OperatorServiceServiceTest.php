<?php

namespace TradeAppOne\Tests\Unit\Domain\Services;

use TradeAppOne\Domain\Services\OperatorServiceService;
use TradeAppOne\Tests\TestCase;

class OperatorServiceServiceTest extends TestCase
{
    /** @test */
    public function test_get_all_services_should_work()
    {
        $operatorServiceServiceMock = $this->createMock(OperatorServiceService::class);

        $operatorServiceServiceMock->method('getAllServices')
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(
            ['foo' => 'bar'], 
            $operatorServiceServiceMock->getAllServices()
        );
    }

    /** @test */
    public function test_get_all_optionals_should_work()
    {
        $operatorServiceServiceMock = $this->createMock(OperatorServiceService::class);

        $operatorServiceServiceMock->method('getAllServiceOptions')
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(
            ['foo' => 'bar'], 
            $operatorServiceServiceMock->getAllServiceOptions()
        );
    }

    /** @test */
    public function test_get_services_and_options_by_attribution_should_work()
    {
        $operatorServiceServiceMock = $this->createMock(OperatorServiceService::class);

        $operatorServiceServiceMock->method('getServicesAndOptionsByAttribution')
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(
            ['foo' => 'bar'], 
            $operatorServiceServiceMock->getServicesAndOptionsByAttribution(1, 'bar')
        );
    }
}
