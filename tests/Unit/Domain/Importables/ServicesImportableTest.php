<?php


namespace TradeAppOne\Tests\Unit\Domain\Importables;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Options;
use TradeAppOne\Domain\Importables\ServicesImportable;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\TestCase;

class ServicesImportableTest extends TestCase
{
    /** @test */
    public function should_enable_autentica_promotor_on_service_options(): void
    {
        $network     = (new NetworkBuilder())->build();
        $hierarchy   = (new HierarchyBuilder())->build();

        $service = factory(Service::class)->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_BANDA_LARGA
            ]);

        factory(ServiceOption::class)->create([
            'action' => Options::AUTENTICA_VENDEDOR
        ]);

        $pointOfSale = (new PointOfSaleBuilder())
            ->withServices($service)
            ->withHierarchy($hierarchy)
            ->withNetwork($network)
            ->build();

        $pointOfSale->providerIdentifiers = '{"CLARO": "XPTO"}';
        $pointOfSale->save();

        $line = $this->getLine();
        $line['action'] = Options::AUTENTICA_VENDEDOR;

        $this->importableServiceOptions()->processLine($line);

        self::assertEquals(
            Options::AUTENTICA_VENDEDOR,
            $pointOfSale->availableServicesRelation()->get()->first()->options()->get()->first()->action
        );
    }

    /** @test */
    public function should_enable_autentica_promotor_when_point_of_sale_has_not_services(): void
    {
        $service = factory(Service::class)->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_BANDA_LARGA
        ]);

        $network   = (new NetworkBuilder())->withServices($service)->build();
        $hierarchy = (new HierarchyBuilder())->build();

        factory(ServiceOption::class)->create([
            'action' => Options::AUTENTICA_VENDEDOR
        ]);

        $pointOfSale = (new PointOfSaleBuilder())
            ->withHierarchy($hierarchy)
            ->withNetwork($network)
            ->build();

        $pointOfSale->providerIdentifiers = '{"CLARO": "XPTO"}';
        $pointOfSale->save();

        $line = $this->getLine();
        $line['action'] = Options::AUTENTICA_VENDEDOR;

        $this->importableServiceOptions()->processLine($line);

        self::assertEquals(
            Options::AUTENTICA_VENDEDOR,
            $pointOfSale->availableServicesRelation()->get()->first()->options()->get()->first()->action
        );
    }

    /** @test */
    public function should_disable_autentica_promotor(): void
    {
        $network     = (new NetworkBuilder())->build();
        $hierarchy   = (new HierarchyBuilder())->build();

        $service = factory(Service::class)->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_BANDA_LARGA
        ]);

        $option = factory(ServiceOption::class)->create([
            'action' => Options::AUTENTICA_VENDEDOR
        ]);

        $pointOfSale = (new PointOfSaleBuilder())
            ->withServices($service)
            ->withHierarchy($hierarchy)
            ->withNetwork($network)
            ->build();

        $pointOfSale->providerIdentifiers = '{"CLARO": "XPTO"}';
        $pointOfSale->save();

        $availableService = AvailableService::findByPointOfSale(
            $pointOfSale, Options::AUTENTICA_RELATION[Options::AUTENTICA_VENDEDOR]);

        $availableService->get()->first()->options()->sync($option);

        $line = $this->getLine();
        $line['action'] = Options::AUTENTICA_VENDEDOR;
        $line['value']  = 0;

        $this->importableServiceOptions()->processLine($line);

        self::assertTrue(
            $pointOfSale->availableServicesRelation()->get()->first()->options()->get()->isEmpty()
        );
    }

    private function getLine(): array
    {
        $importableService = $this->importableServiceOptions();

        return array_combine(
            array_keys($importableService->getColumns()),
            $importableService->getExample()
        );
    }

    protected function importableServiceOptions(): ServicesImportable
    {
        return resolve(ServicesImportable::class);
    }
}
