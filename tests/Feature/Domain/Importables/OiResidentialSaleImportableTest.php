<?php

declare(strict_types=1);

namespace TradeAppOne\Tests\Feature\Domain\Importables;

use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Enumerators\Permissions\ImportablePermission;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;
use TradeAppOne\Exceptions\SystemExceptions\OiResidentialSaleImportableExceptions;
use TradeAppOne\Facades\SyncUserOperators;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\OiResidentialSaleImportableBuilderHelper;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class OiResidentialSaleImportableTest extends TestCase
{
    use AuthHelper;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3');
        SyncUserOperators::shouldReceive('sync')->atLeast();
    }

    public function test_should_return_csv_example(): void
    {
        $user = (new UserBuilder())->build();
        $response = $this->withHeader('Authorization', $this->loginUser($user))
            ->get('/sales/importOiResidential');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->getContent());
    }

    public function test_should_save_sale_when_csv_imported(): void
    {
        $user = $this->getUser($this->getPointOfSale());

        $file = $this->getInstanceOiResidentialSaleImportableBuilder()
            ->buildFile(
                $user->cpf ?? null,
                $this->getPointOfSale()->cnpj ?? null
            );

        $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson('/sales/importOiResidential', ['file' => $file])->assertStatus(201);
    }

    public function test_should_throw_exception_salesman_not_found_when_csv_imported(): void
    {
        $user = $this->getUser($this->getPointOfSale());

        $file = $this->getInstanceOiResidentialSaleImportableBuilder()
            ->buildFile(
                '25699872809',
                $this->getPointOfSale()->cnpj ?? null
            );

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson('/sales/importOiResidential', ['file' => $file]);

        $error = str_getcsv($response->getContent(), ';');

        $this->assertSame(
            end($error),
            OiResidentialSaleImportableExceptions::salesmanNotFound()->getMessage()
        );
        $this->assertNotEmpty($response->getContent());
    }

    public function test_should_throw_exception_pdv_not_found_when_csv_imported(): void
    {
        $user = $this->getUser($this->getPointOfSale());

        $file = $this->getInstanceOiResidentialSaleImportableBuilder()
            ->buildFile(
                $user->cpf,
                '84167820000162'
            );

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson('/sales/importOiResidential', ['file' => $file]);

        $error = str_getcsv($response->getContent(), ';');

        $this->assertSame(
            end($error),
            (new PointOfSaleNotFoundException())->getMessage()
        );
        $this->assertNotEmpty($response->getContent());
    }

    public function test_should_throw_exception_sale_already_exists_when_csv_imported(): void
    {
        $user = $this->getUser($this->getPointOfSale());

        $file = $this->getInstanceOiResidentialSaleImportableBuilder()
            ->buildFile(
                $user->cpf,
                $this->getPointOfSale()->cnpj
            );

        $this->withHeader('Authorization', $this->loginUser($user))
            ->postJson('/sales/importOiResidential', ['file' => $file]);

        $response = $this->withHeader('Authorization', $this->loginUser($user))
            ->postJson('/sales/importOiResidential', ['file' => $file]);

        $error = str_getcsv($response->getContent(), ';');

        $this->assertSame(
            end($error),
            OiResidentialSaleImportableExceptions::saleAlreadyExists()->getMessage()
        );
        $this->assertNotEmpty($response->getContent());
    }

    private function getInstanceOiResidentialSaleImportableBuilder(): OiResidentialSaleImportableBuilderHelper
    {
        return resolve(OiResidentialSaleImportableBuilderHelper::class);
    }

    private function getPointOfSale(): PointOfSale
    {
        return (new PointOfSaleBuilder())->build();
    }

    private function getUser(PointOfSale $pointOfSale): User
    {
        return (new UserBuilder())
            ->withPointOfSale($pointOfSale)
            ->withPermission(
                ImportablePermission::getFullName(ImportablePermission::OI_RESIDENTIAL_SALE)
            )
            ->build();
    }
}
