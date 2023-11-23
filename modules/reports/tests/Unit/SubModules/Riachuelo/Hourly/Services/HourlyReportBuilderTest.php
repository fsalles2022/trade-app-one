<?php

declare(strict_types=1);

namespace Reports\Tests\Unit\SubModules\Riachuelo\Hourly\Services;

use Reports\SubModules\Core\Models\Hierarchies;
use Reports\SubModules\Core\Models\Operators;
use Reports\SubModules\Core\Models\Sales;
use Reports\SubModules\Riachuelo\Hourly\Services\HierarchySaleAccumulator;
use Reports\SubModules\Riachuelo\Hourly\Services\HourlyReportBuilder;
use Reports\SubModules\Riachuelo\Hourly\Services\PointOfSaleSaleAccumulator;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\TestCase;

class HourlyReportBuilderTest extends TestCase
{
    public function test_should_return_quantity_of_sales_by_hierarquies_and_points_of_sale(): void
    {
        $operators    = new Operators([Operations::CLARO]);
        $network      = NetworkBuilder::make()->withSlug(NetworkEnum::RIACHUELO)->build();
        $hierarchy    = HierarchyBuilder::make()->withNetwork($network)->build();
        $pointsOfSale = PointOfSaleBuilder::make()->withHierarchy($hierarchy)->build();

        $service = factory(Service::class)->create(
            [
                'operator' => Operations::CLARO,
                'operation' => Operations::CLARO_CONTROLE_BOLETO,
                'mode' => 'ACTIVATION',
            ]
        );

        $sale = factory(Sale::class)->create(
            [
                'services' => [$service->toArray()],
                'pointOfSale' => $pointsOfSale->toArray(),
                'createdAt' => now()->subHour(1),
                'updatedAt' => now()->subHour(1)
            ]
        );

        $hourlyReportBuilder = new HourlyReportBuilder(
            new Sales([$sale]),
            new Hierarchies([$hierarchy]),
            $operators
        );

        $hourlyReportBuilder->build();
        $hierarchiesSaleAccumulators  = $hourlyReportBuilder->getHierarchiesSaleAccumulators();
        $pointsOfSaleSaleAccumulators = $hourlyReportBuilder->getPointsOfSaleSaleAccumulators();

        $this->assertCount(1, $hierarchiesSaleAccumulators);
        $this->assertCount(1, $pointsOfSaleSaleAccumulators);
        $this->assertInstanceOf(
            HierarchySaleAccumulator::class,
            $hierarchiesSaleAccumulators[$hierarchy->slug]
        );
        $this->assertInstanceOf(
            PointOfSaleSaleAccumulator::class,
            $pointsOfSaleSaleAccumulators[$pointsOfSale->cnpj]
        );
        $this->assertEquals(
            1,
            $hierarchiesSaleAccumulators[$hierarchy->slug]->getTotalVolumeByOperator(Operations::CLARO)
        );
        $this->assertEquals(
            1,
            $hierarchiesSaleAccumulators[$hierarchy->slug]->getTotalVolumeAccumulator()
        );
        $this->assertEquals(
            1,
            $pointsOfSaleSaleAccumulators[$pointsOfSale->cnpj]->getTotalVolumeByOperator(Operations::CLARO)
        );
        $this->assertEquals(
            1,
            $pointsOfSaleSaleAccumulators[$pointsOfSale->cnpj]->getTotalVolumeAccumulator()
        );
        $this->assertInstanceOf(
            PointOfSale::class,
            $pointsOfSaleSaleAccumulators[$pointsOfSale->cnpj]->getPointOfSale()
        );
        $this->assertInstanceOf(
            Hierarchy::class,
            $hierarchiesSaleAccumulators[$hierarchy->slug]->getHierarchy()
        );
    }
}
