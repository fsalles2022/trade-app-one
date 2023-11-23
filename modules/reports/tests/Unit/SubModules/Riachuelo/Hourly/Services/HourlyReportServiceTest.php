<?php

declare(strict_types=1);

namespace Reports\Tests\Unit\SubModules\Riachuelo\Hourly\Services;

use Reports\SubModules\Riachuelo\Hourly\Services\HourlyReportService;
use TradeAppOne\Domain\Components\Telegram\Telegram;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use Reports\Tests\Helpers\BindInstance;
use TradeAppOne\Tests\TestCase;

class HourlyReportServiceTest extends TestCase
{
    use BindInstance;

    public function test_should_return_filepath_and_filepath_exists(): void
    {
        $this->mockInstanceTelegram();
        $network      = NetworkBuilder::make()->withSlug(NetworkEnum::RIACHUELO)->build();
        $hierarchy    = HierarchyBuilder::make()->withNetwork($network)->build();
        $pointsOfSale = PointOfSaleBuilder::make()->withHierarchy($hierarchy)->build();

        $services = factory(Service::class)->create(
            [
                'operator' => Operations::CLARO,
                'operation' => Operations::CLARO_CONTROLE_BOLETO,
                'mode' => 'ACTIVATION',
            ]
        );

        factory(Sale::class)->create(
            [
                'services' => [$services->toArray()],
                'pointOfSale' => $pointsOfSale->toArray(),
                'createdAt' => now()->subHour(1),
                'updatedAt' => now()->subHour(1)
            ]
        );

        $filePath = $this->getInstanceHourlyReportService()->report();
        $this->assertNotNull($filePath);
        $this->assertFileExists($filePath);
    }

    public function test_should_generate_report_without_sales(): void
    {
        $this->mockInstanceTelegram();
        $network   = NetworkBuilder::make()->withSlug(NetworkEnum::RIACHUELO)->build();
        $hierarchy = HierarchyBuilder::make()->withNetwork($network)->build();
        PointOfSaleBuilder::make()->withHierarchy($hierarchy)->build();

        $filePath = $this->getInstanceHourlyReportService()->report();
        $this->assertNotNull($filePath);
        $this->assertFileExists($filePath);
    }

    private function mockInstanceTelegram(): void
    {
        $this->bindInstance(Telegram::class)
            ->shouldReceive('sendDocument');
    }

    private function getInstanceHourlyReportService(): HourlyReportService
    {
        return resolve(HourlyReportService::class);
    }
}
