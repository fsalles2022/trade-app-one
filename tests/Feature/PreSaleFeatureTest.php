<?php


namespace TradeAppOne\Tests\Feature;

use ClaroBR\Tests\Helpers\ClaroServices;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class PreSaleFeatureTest extends TestCase
{
    use AuthHelper;

    private const DEFAULT_IMEI_TEST = '538665262611099';

    // TODO Fix this test, on it call the homologation siv, needs include a mock response
    public function should_update_pre_sale_imei(): void
    {
        $permissions = factory(Permission::class)->create([
            'slug'   => 'SALE.EDIT'
        ]);

        $network = factory(Network::class)->create();

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user    = (new UserBuilder())
            ->withPermissions([$permissions])
            ->withPointOfSale($pointOfSale)
            ->build();
        $user->cpf = '04734324123';
        $user->save();
        $controleBoletoService = ClaroServices::ControleBoleto();
        $controleBoletoService->isPreSale = true;
        $controleBoletoService->operatorIdentifiers = [
            'venda_id' => 9174080,
            'servico_id' => 8875251
        ];

        $sale = (new SaleBuilder())
            ->withServices([$controleBoletoService])
            ->withPointOfSale($pointOfSale)
            ->build();

        $serviceId = $sale->services->first()->serviceTransaction;

        $this->authAs($user)
            ->put('/sales/pre-sale', [
                'imei' => self::DEFAULT_IMEI_TEST,
                'serviceTransaction' => $serviceId,
            ])->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('sales', [
            'services.serviceTransaction' => $serviceId,
            'services.imei' => self::DEFAULT_IMEI_TEST
        ], 'mongodb');
    }

    /** @test */
    public function should_not_update_pre_sale_with_sale_not_found(): void
    {
        $permissions = factory(Permission::class)->create([
            'slug'   => 'SALE.EDIT'
        ]);

        $network = factory(Network::class)->create();

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user    = (new UserBuilder())
            ->withPermissions([$permissions])
            ->withPointOfSale($pointOfSale)
            ->build();

        $controleBoletoService = ClaroServices::ControleBoleto();
        $controleBoletoService->isPreSale = true;

        (new SaleBuilder())
            ->withServices([$controleBoletoService])
            ->withPointOfSale($pointOfSale)
            ->build();
        $serviceId = '2020125125-8';

        $response = $this->authAs($user)
            ->put('/sales/pre-sale', [
                'imei' => self::DEFAULT_IMEI_TEST,
                'serviceTransaction' => $serviceId,
            ]);

        $this->assertEquals($response->json('error'), trans('messages.preSale.notFound'));
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /** @test */
    public function should_not_update_pre_sale_when_not_pre_sale(): void
    {
        $permissions = factory(Permission::class)->create([
            'slug'   => 'SALE.EDIT'
        ]);

        $network = factory(Network::class)->create();

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user    = (new UserBuilder())
            ->withPermissions([$permissions])
            ->withPointOfSale($pointOfSale)
            ->build();

        $controleBoletoService = ClaroServices::ControleBoleto();
        $controleBoletoService->isPreSale = false;

        $sale      = (new SaleBuilder())
            ->withServices([$controleBoletoService])
            ->withPointOfSale($pointOfSale)
            ->build();
        $serviceId = $sale->services->first()->serviceTransaction;

        $response = $this->authAs($user)
            ->put('/sales/pre-sale', [
                'imei' => self::DEFAULT_IMEI_TEST,
                'serviceTransaction' => $serviceId,
            ]);
        $this->assertEquals($response->json('data.preSaleUpdated'), false);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }
}
