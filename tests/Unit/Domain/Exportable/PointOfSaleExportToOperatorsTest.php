<?php
namespace TradeAppOne\Tests\Unit\Domain\Exportable;
use Illuminate\Http\Response;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class PointOfSaleExportToOperatorsTest extends TestCase
{
    use AuthHelper;
    /** @test */
    public function should_return_csv_with_claro_pointOfSales_and_filtered(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $pointOfSale->update(['providerIdentifiers' => json_encode(['CLARO' => 'NPX1'])]);
        $response = $this->authAs($user)
            ->post('/export/claro/pointsofsale', ['networks' => [$network->slug]])
            ->assertStatus(Response::HTTP_OK);
        $this->assertContains($pointOfSale->cnpj, $response->content());
    }

    /** @test */
    public function should_return_csv_with_tim_pointOfSales_and_filtered(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $pointOfSale->update(['providerIdentifiers' => json_encode(['TIM' => 'SP10_MGOESI_VA0008_A515'])]);
        $response = $this->authAs($user)
            ->post('/export/timbr/pointsofsale', ['networks' => [$network->slug]])
            ->assertStatus(Response::HTTP_OK);
        $this->assertContains($pointOfSale->providerIdentifiers['TIM'], $response->content());
    }
}