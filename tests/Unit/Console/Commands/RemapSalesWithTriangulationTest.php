<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use TradeAppOne\Console\Commands\InputCommand;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class RemapSalesWithTriangulationTest extends TestCase
{
    /** @test */
    public function should_save_log_fail_when_attributes_invalids()
    {
        $this->mockAsk();

        $service = $this->getService('TYPE-INCORRECT');

        (new SaleBuilder())
            ->withServices($service)
            ->build();

        $this->artisan('sales:remap-triangulation');

        $this->assertDatabaseHas('sales', ['services.device.discount.id' => 14], 'mongodb');
        $this->assertDatabaseHas('sales', ['services.device.products.id' => 14], 'mongodb');
        $this->assertDatabaseHas('sales', ['services.log.triangulationRemap' => 'FAIL'], 'mongodb');
    }

    /** @test */
    public function should_save_log_done_when_attributes_valid()
    {
        $this->mockAsk();

        $service = $this->getService();

        (new SaleBuilder())
            ->withServices($service)
            ->build();

        $this->artisan('sales:remap-triangulation');

        $this->assertDatabaseMissing('sales', ['services.device.discount.id' => 14], 'mongodb');
        $this->assertDatabaseMissing('sales', ['services.device.products.id' => 14], 'mongodb');
        $this->assertDatabaseHas('sales', ['services.log.triangulationRemap' => 'DONE'], 'mongodb');
        $this->assertDatabaseHas('sales', ['services.discount.id' => 14], 'mongodb');
    }

    /** @test */
    public function should_update_only_services_with_operator_claro()
    {
        $this->mockAsk();
        $service = $this->getService(100, Operations::VIVO);

        (new SaleBuilder())
            ->withServices($service)
            ->build();

        $this->artisan('sales:remap-triangulation');

        $this->assertDatabaseHas('sales', ['services.device.discount.id' => 14], 'mongodb');
        $this->assertDatabaseHas('sales', ['services.device.products.id' => 14], 'mongodb');
        $this->assertDatabaseMissing('sales', ['services.log.triangulationRemap' => 'DONE'], 'mongodb');
        $this->assertDatabaseMissing('sales', ['services.log.triangulationRemap' => 'FAIL'], 'mongodb');
        $this->assertDatabaseMissing('sales', ['services.discount.id' => 14], 'mongodb');
    }

    /** @test */
    public function should_update_only_services_with_triangulation()
    {
        $this->mockAsk();
        $service = $this->getService();
        $service[0]->device = [];

        (new SaleBuilder())
            ->withServices($service)
            ->build();

        $this->artisan('sales:remap-triangulation');

        $this->assertDatabaseMissing('sales', ['services.log.triangulationRemap' => 'DONE'], 'mongodb');
        $this->assertDatabaseMissing('sales', ['services.log.triangulationRemap' => 'FAIL'], 'mongodb');
    }

    private function mockAsk($response = true)
    {
        $mock = \Mockery::mock(InputCommand::class)->makePartial();
        $mock->shouldReceive('confirmSaleQuantity')->andReturnTrue();
        $this->instance(InputCommand::class, $mock);
    }

    private function getService($price = 799, $operator = 'CLARO'): array
    {
        $attributes = array (
                    'operator' => $operator,
                    'operation' => 'CONTROLE_FACIL',
                    'mode' => 'MIGRATION',
                    'product' => 94,
                    'msisdn' => '67992160309',
                    'imei' => '353783101787343',
                    'device' =>
                        array (
                            'id' => 563,
                            'label' => 'SAMSUNG GALAXY J2 CORE 16GB-PRATA',
                            'sku' => '7892509104708',
                            'discount' =>
                                array (
                                    'title' => 'J2 Core Claro Controle',
                                    'id' => 14,
                                    'price' => $price,
                                    'discount' => 100,
                                    'products' =>
                                        array (
                                            0 =>
                                                array (
                                                    'id' => 14,
                                                    'operator' => 'CLARO',
                                                    'operation' => 'CONTROLE_BOLETO',
                                                    'product' => NULL,
                                                    'filterMode' => 'ALL',
                                                    'label' => NULL,
                                                    'discountId' => 14,
                                                    'createdAt' => '2019-07-26 18:30:08',
                                                    'updatedAt' => '2019-07-26 18:30:08',
                                                    'deletedAt' => NULL,
                                                    'title' => 'J2 Core Claro Controle',
                                                    'price' => 799,
                                                    'discount' => 100,
                                                ),
                                            1 =>
                                                array (
                                                    'id' => 14,
                                                    'operator' => 'CLARO',
                                                    'operation' => 'CONTROLE_FACIL',
                                                    'product' => NULL,
                                                    'filterMode' => 'ALL',
                                                    'label' => NULL,
                                                    'discountId' => 14,
                                                    'createdAt' => '2019-07-26 18:30:08',
                                                    'updatedAt' => '2019-07-26 18:30:08',
                                                    'deletedAt' => NULL,
                                                    'title' => 'J2 Core Claro Controle',
                                                    'price' => 799,
                                                    'discount' => 100,
                                                ),
                                        ),
                                ),
                            'products' =>
                                array (
                                    0 =>
                                        array (
                                            'id' => 14,
                                            'operator' => 'CLARO',
                                            'operation' => 'CONTROLE_BOLETO',
                                            'product' => NULL,
                                            'filterMode' => 'ALL',
                                            'label' => NULL,
                                            'discountId' => 14,
                                            'createdAt' => '2019-07-26 18:30:08',
                                            'updatedAt' => '2019-07-26 18:30:08',
                                            'deletedAt' => NULL,
                                            'title' => 'J2 Core Claro Controle',
                                            'price' => 799,
                                            'discount' => 100,
                                        ),
                                    1 =>
                                        array (
                                            'id' => 14,
                                            'operator' => 'CLARO',
                                            'operation' => 'CONTROLE_FACIL',
                                            'product' => NULL,
                                            'filterMode' => 'ALL',
                                            'label' => NULL,
                                            'discountId' => 14,
                                            'createdAt' => '2019-07-26 18:30:08',
                                            'updatedAt' => '2019-07-26 18:30:08',
                                            'deletedAt' => NULL,
                                            'title' => 'J2 Core Claro Controle',
                                            'price' => 799,
                                            'discount' => 100,
                                        ),
                                ),
                        ),
                    'customer' =>
                        array (
                            'cpf' => '18281775807',
                            'firstName' => 'masisa',
                            'lastName' => 'marcilio garrucho',
                        ),
                    'sector' => 'TELECOMMUNICATION',
                    'label' => 'Claro Controle Plus 3GB + Minutos Ilimitados',
                    'serviceTransaction' => '201907291914373848-0',
                    'log' =>
                        array (
                            0 =>
                                array (
                                    'type' => 'success',
                                    'message' => 'Serviço ativado com sucesso',
                                ),
                        ),
                );

        return [factory(Service::class)->make($attributes)];
    }

}
