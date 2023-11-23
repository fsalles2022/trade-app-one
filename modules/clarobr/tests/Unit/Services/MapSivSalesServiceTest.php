<?php

namespace ClaroBR\Tests\Unit\Services;

use ClaroBR\Enumerators\SivOperations;
use ClaroBR\Services\MapSivSalesService;
use ClaroBR\Tests\Unit\Fixtures\SivSalesFixture;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\ModesTranslation;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\TestCase;

class MapSivSalesServiceTest extends TestCase
{
    const WEB = 'WEB';
    const APP = 'APP';
    const API = 'API';

    /** @test */
    public function should_return_claro_pos_in_service_operation()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $sale = SivSalesFixture::oneSale($pointOfSale->cnpj, ModesTranslation::MIGRATION);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEquals(Operations::CLARO_POS, data_get($result->first(), 'service_operation'));
    }

    /** @test */
    public function should_return_claro_pre_in_service_operation()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $sale = SivSalesFixture::oneSale($pointOfSale->cnpj, ModesTranslation::MIGRATION, self::WEB, SivOperations::PRE_PAGO);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEquals(Operations::CLARO_PRE, data_get($result->first(), 'service_operation'));
    }

    /** @test */
    public function should_return_claro_controle_boleto_in_service_operation()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $sale = SivSalesFixture::oneSale($pointOfSale->cnpj, ModesTranslation::MIGRATION, self::WEB, SivOperations::CONTROLE_BOLETO);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEquals(Operations::CLARO_CONTROLE_BOLETO, data_get($result->first(), 'service_operation'));
    }

    /** @test */
    public function should_return_claro_controle_facil_in_service_operation()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $sale = SivSalesFixture::oneSale($pointOfSale->cnpj, ModesTranslation::MIGRATION, self::WEB, SivOperations::CONTROLE_FACIL);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEquals(Operations::CLARO_CONTROLE_FACIL, data_get($result->first(), 'service_operation'));
    }

    /** @test */
    public function should_return_migration_in_service_mode()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        $sale = SivSalesFixture::oneSale($pointOfSale->cnpj, ModesTranslation::MIGRATION);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEquals(Modes::MIGRATION, data_get($result->first(), 'service_mode'));
    }

    /** @test */
    public function should_return_activation_in_service_mode()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $sale        = SivSalesFixture::oneSale($pointOfSale->cnpj, ModesTranslation::ACTIVATION);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEquals(Modes::ACTIVATION, data_get($result->first(), 'service_mode'));
    }

    /** @test */
    public function should_return_empy_if_point_of_sale_not_exists()
    {
        $invalidCNPJ = '00000000000000';
        $sale        = SivSalesFixture::oneSale($invalidCNPJ, ModesTranslation::ACTIVATION);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEmpty($result);
    }

    /** @test */
    public function should_return_empy_if_source_is_not_WEB_or_APP()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $sale        = SivSalesFixture::oneSale($pointOfSale->cnpj, ModesTranslation::ACTIVATION, self::API);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEmpty($result);
    }

    /** @test */
    public function should_return_one_when_source_is_web()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $sale        = SivSalesFixture::oneSale($pointOfSale->cnpj, ModesTranslation::ACTIVATION);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEquals(1, $result->count());
    }

    /** @test */
    public function should_return_one_when_source_is_app()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $sale        = SivSalesFixture::oneSale($pointOfSale->cnpj, ModesTranslation::ACTIVATION, self::APP);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEquals(1, $result->count());
    }

    /** @test */
    public function should_return_two_sales_when_sale_have_dependent()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $sale        = SivSalesFixture::oneSaleWithDependents($pointOfSale->cnpj, ModesTranslation::ACTIVATION, self::APP);

        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));
        self::assertEquals(2, $result->count());
    }

    /** @test */
    public function should_return_sale_with_promotion()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $sale        = SivSalesFixture::oneSale($pointOfSale->cnpj, ModesTranslation::MIGRATION);
        $importation = resolve(MapSivSalesService::class);
        $result      = $importation->mapToTable(collect($sale));

        self::assertEquals(
            'PREZÃO 19,99 POR MÊS: Ligações ilimitadas para Claro + 500 minutos para outras operadoras + 3GB + WhatsApp sem gastar internet.',
            data_get($result->first(), 'service_promotion_label')
        );
    }
}
