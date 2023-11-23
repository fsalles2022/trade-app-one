<?php

namespace Core\PowerBi\tests\Unit;

use Core\PowerBi\Services\PowerBiService;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

class PowerBiServiceTest extends TestCase
{
    /** @test */
    public function should_return_values_empty_for_user_with_context_all()
    {
        $user = (new UserBuilder())->withPermission(SalePermission::getFullName(SalePermission::CONTEXT_ALL))->build();

        $powerBiService = app()->make(PowerBiService::class);
        $filters        = $powerBiService->getFilters($user);
        self::assertCount(1, $filters);
        self::assertArrayHasKey('filter', $filters[0]);
        self::assertArrayHasKey('values', $filters[0]);
        self::assertEquals(PowerBiService::POINTOFSALE_CNPJ, data_get($filters, '0.filter'));
        self::assertEmpty(data_get($filters, '0.values'));
    }

    /** @test */
    public function should_return_values_with_cnpjs_for_user_with_context_hierarchy()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $user        = (new UserBuilder())
            ->withPointOfSale($pointOfSale)
            ->withPermission(SalePermission::getFullName(SalePermission::CONTEXT_HIERARCHY))
            ->build();

        $powerBiService = app()->make(PowerBiService::class);

        $filters = $powerBiService->getFilters($user);

        self::assertCount(1, $filters);
        self::assertArrayHasKey('filter', $filters[0]);
        self::assertArrayHasKey('values', $filters[0]);
        self::assertEquals(PowerBiService::POINTOFSALE_CNPJ, data_get($filters, '0.filter'));
        self::assertNotEmpty(data_get($filters, '0.values'));
    }

    /** @test */
    public function should_return_values_with_user_cpf_for_user_with_no_context()
    {
        $user = (new UserBuilder())->build();

        $powerBiService = app()->make(PowerBiService::class);

        $filters = $powerBiService->getFilters($user);

        self::assertCount(1, $filters);
        self::assertArrayHasKey('filter', $filters[0]);
        self::assertContains(data_get($filters, '0.filter'), [
            PowerBiService::USER_CPF,
            PowerBiService::DLAD_NOME
        ]);
        self::assertArrayHasKey('values', $filters[0]);
        self::assertNotEmpty(data_get($filters, '0.values'));
    }
}
