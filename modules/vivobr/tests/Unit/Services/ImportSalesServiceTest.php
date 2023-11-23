<?php

namespace VivoBR\Tests\Unit\Services;

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Services\SaleImportService;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\TestCase;
use VivoBR\Connection\SunConnection;
use VivoBR\Services\ImportSalesFromVivoService;
use VivoBR\Services\MapSunSalesService;
use VivoBR\Tests\Fixtures\SalesFromSun;
use VivoBR\Tests\Helpers\VivoFactoriesHelper;

class ImportSalesServiceTest extends TestCase
{
    use VivoFactoriesHelper;

    /** @test */
    public function should_execute_without_exception()
    {
        $sales   = collect(SalesFromSun::allSalesFromNetworks()['vendas']);
        $fixture = ['vendas' => $sales->slice(0, 1)];

        $restResponse = $this->getMockBuilder(RestResponse::class)
            ->disableOriginalConstructor()
            ->setMethods(array('toArray'))
            ->getMock();
        $restResponse->expects($this->once())->method('toArray')->will($this->returnValue($fixture));

        $connectionMock = $this->getMockBuilder(SunConnection::class)
            ->disableOriginalConstructor()
            ->setMethods(array('querySales'))
            ->getMock();
        $connectionMock->expects($this->once())->method('querySales')->will($this->returnValue($restResponse));

        $mock = $this->getMockBuilder(SunConnection::class)
            ->disableOriginalConstructor()
            ->setMethods(array('selectCustomConnection'))
            ->getMock();
        $mock->expects($this->once())->method('selectCustomConnection')->will($this->returnValue($connectionMock));

        $importation = new ImportSalesFromVivoService(
            $mock,
            new MapSunSalesService(),
            resolve(SaleImportService::class)
        );
        $importation->execute(['network' => 'all']);
    }

    /** @test */
    public function should_return_315_filtered_when_sun_return_same_sale()
    {
        $sunResponse = \Mockery::mock(RestResponse::class)->makePartial();
        $sunResponse->shouldReceive('toArray')->andReturn(SalesFromSun::allSalesFromNetworks());

        $sunConnection = \Mockery::mock(SunConnection::class)->makePartial();
        $sunConnection->shouldReceive('selectCustomConnection')->andReturnSelf();
        $sunConnection->shouldReceive('querySales')->andReturn($sunResponse);

        $import = new ImportSalesFromVivoService($sunConnection, new MapSunSalesService());
        $result = $import->execute(['network' => NetworkEnum::CEA]);
        self::assertEquals(SalesFromSun::TOTAL_NOT_API_SERVICES, $result->count());
    }

    /** @test */
    public function should_return_zero_sale_filtered_when_sun_return_same_sale()
    {
        $sunResponse = \Mockery::mock(RestResponse::class)->makePartial();
        $sunResponse->shouldReceive('toArray')->andReturn(SalesFromSun::allSalesFromNetworks());

        $sunConnection = \Mockery::mock(SunConnection::class)->makePartial();
        $sunConnection->shouldReceive('selectCustomConnection')->andReturnSelf();
        $sunConnection->shouldReceive('querySales')->andReturn($sunResponse);

        $import = new ImportSalesFromVivoService($sunConnection, new MapSunSalesService());
        $result = $import->execute(['network' => NetworkEnum::CEA]);
        self::assertEquals(SalesFromSun::TOTAL_NOT_API_SERVICES, $result->count());
    }

    /** @test */
    public function should_return_315_sale_created_when_sun_return_same_sale()
    {
        $sunResponse = \Mockery::mock(RestResponse::class)->makePartial();
        $sunResponse->shouldReceive('toArray')->andReturn(SalesFromSun::allSalesFromNetworks());

        $sunConnection = \Mockery::mock(SunConnection::class)->makePartial();
        $sunConnection->shouldReceive('selectCustomConnection')->andReturnSelf();
        $sunConnection->shouldReceive('querySales')->andReturn($sunResponse);

        $import = new ImportSalesFromVivoService($sunConnection, new MapSunSalesService());
        $result = $import->execute(['network' => NetworkEnum::CEA]);
        self::assertEquals(SalesFromSun::TOTAL_NOT_API_SERVICES, $result->count());
        self::assertEquals(SalesFromSun::TOTAL_NOT_API_SERVICES, $result->where('action', 'create')->count());
    }

    /** @test */
    public function should_updated_315_sale_created_when_point_of_sale_added_to_system()
    {
        $sunResponse = \Mockery::mock(RestResponse::class)->makePartial();
        $sunResponse->shouldReceive('toArray')->andReturn(SalesFromSun::allSalesFromNetworks());

        $sunConnection = \Mockery::mock(SunConnection::class)->makePartial();
        $sunConnection->shouldReceive('selectCustomConnection')->andReturnSelf();
        $sunConnection->shouldReceive('querySales')->andReturn($sunResponse);

        $import = new ImportSalesFromVivoService($sunConnection, new MapSunSalesService());
        $import->execute(['network' => 'cea']);

        $pointOfSaleExistentInMock       = (new PointOfSaleBuilder())->build();
        $cnpjPresentOnMock               = '61099834040222';
        $pointOfSaleExistentInMock->cnpj = $cnpjPresentOnMock;
        $pointOfSaleExistentInMock->save();

        $result = $import->execute(['network' => 'cea']);

        self::assertEquals(SalesFromSun::TOTAL_NOT_API_SERVICES, $result->count());
    }
}
