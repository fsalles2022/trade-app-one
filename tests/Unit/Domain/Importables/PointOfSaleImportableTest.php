<?php

namespace TradeAppOne\Tests\Unit\Domain\Importables;


use NextelBR\Enumerators\NextelBRConstants;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Importables\PointOfSaleImportable;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\TestCase;

class PointOfSaleImportableTest extends TestCase
{
    /** @test */
    public function should_return_point_of_sale_instance_when_completed()
    {
        $network     = (new NetworkBuilder())->build();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->withNetwork($network)->build();
        $Importable  = $this->importablePOS();

        $line               = $this->getLine($network, $pointOfSale, $hierarchy);
        $pointOfSaleCreated = $Importable->processLine($line);
        $className          = get_class($pointOfSaleCreated);

        $this->assertEquals(PointOfSale::class, $className);
    }

    /** @test */
    public function should_create_a_new_point_of_sale_if_there_is_no()
    {
        $importable = $this->importablePOS();
        $network    = (factory(Network::class)->create());
        $hierarchy  = (new HierarchyBuilder())->withNetwork($network)->build();

        $line = $this->getLine($network, null, $hierarchy);
        $importable->processLine($line);

        $this->assertDatabaseHas('pointsOfSale', [
            'cnpj' => data_get($line, 'cnpj'),
            'hierarchyId' => $hierarchy->id
        ]);
    }

    /** @test */
    public function should_update_provider_identifiers_in_point_of_sale_if_it_exists()
    {
        $importable  = $this->importablePOS();
        $network     = (new NetworkBuilder())->build();
        $hierarchy   = (new HierarchyBuilder())->withNetwork($network)->build();
        $pointOfSale = (new PointOfSaleBuilder())->withState('with_identifiers')->withNetwork($network)->build();

        $oldProviderIdentifiers = $pointOfSale->providerIdentifiers;
        $pointOfSale->providerIdentifiers = [
            Operations::CLARO => '0000',
            Operations::OI => '0000',
            Operations::TIM => 'SP_00',
            Operations::NEXTEL => [
                NextelBRConstants::POINT_OF_SALE_COD => '0000',
                NextelBRConstants::POINT_OF_SALE_REF => '0000'
            ]
        ];

        $line = $this->getLine($network, $pointOfSale, $hierarchy);

        $pointOfSaleCreated = $importable->processLine($line);
        $this->assertNotEquals($pointOfSaleCreated->providerIdentifiers, $oldProviderIdentifiers);
    }

    private function getLine(Network $network, PointOfSale $pointOfSale = null, Hierarchy $hierarchy = null)
    {
        $pointOfSaleImportable = $this->importablePOS();

        $columns = array_keys($pointOfSaleImportable->getColumns());
        $lines = $pointOfSaleImportable->getExample($network->slug, $pointOfSale, $hierarchy->slug);
        return array_combine($columns, $lines);
    }

    protected function importablePOS(): PointOfSaleImportable
    {
        return resolve(PointOfSaleImportable::class);
    }
}
