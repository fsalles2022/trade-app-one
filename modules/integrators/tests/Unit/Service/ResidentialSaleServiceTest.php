<?php


namespace Integrators\tests\Unit\Service;

use ErrorException;
use Integrators\Services\ResidentialSaleService;
use Integrators\tests\Fixtures\SaleFromSiv;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ResidentialSaleServiceTest extends TestCase
{
    /** @var ResidentialSaleService */
    protected $residentialService;

    public function setUp()
    {
        parent::setUp();
        $this->setupPreConditionsToImport();
        $this->residentialService = resolve(ResidentialSaleService::class);
    }

    /** @test */
    public function should_return_sale_when_saved(): void
    {
        $saleSiv = SaleFromSiv::residentialSale();
        $sale    = $this->residentialService->save($saleSiv);
        $this->assertInstanceOf(Sale::class, $sale);
    }

    /** @test */
    public function should_throw_exception_when_sale_wrong(): void
    {
        $this->expectException(ErrorException::class);
        $this->residentialService->save([]);
    }

    private function setupPreConditionsToImport(): void
    {
        $user = (new UserBuilder())->withCustomParameters([
            'cpf' => '01296802140'
        ])->build();

        (new PointOfSaleBuilder())
            ->withState('with_identifiers')
            ->withUser($user)
            ->build();
    }
}
