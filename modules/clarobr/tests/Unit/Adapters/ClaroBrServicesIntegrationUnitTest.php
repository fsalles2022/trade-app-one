<?php


namespace ClaroBR\Tests\Unit\Adapters;

use ClaroBR\Tests\Helpers\ClaroServices;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Factories\ServicesIntegrationResponseFactory;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroBrServicesIntegrationUnitTest extends TestCase
{
    /** @test */
    public function should_success_save_sale_when_controle_facil_has_not_remote_sale(): void
    {
        $sale = factory(Sale::class)->create([
            'services' => [ClaroServices::ControleFacil()->toArray()]
        ]);

        $response = response()->json([
            'messages' => trans('messages.sale_saved')
        ], Response::HTTP_OK);

        $creator = ServicesIntegrationResponseFactory::make($sale->services->first(), $response);

        self::assertEquals('Venda salva com sucesso', $creator->getData()->messages);
    }

    /** @test */
    public function should_success_save_sale_when_controle_facil_has_remote_sale(): void
    {
        $urlOrigin                      = 'http://localhost:8081';
        $serviceStructure               = ClaroServices::ControleFacil()->toArray();
        $serviceStructure['remoteSale'] = true;

        $service = factory(Service::class)->create($serviceStructure);
        $sale    = (new SaleBuilder())->withServices($service)->build();

        $response = response()->json([
            'messages' => trans('messages.sale_saved'),
            'data' => [
                'link' => '{"url":"https://homologacao.tradeapp.com.br/pagamento/018579a0-ad01"}'
            ],
            'urlOrigin' => $urlOrigin
        ], Response::HTTP_OK);

        $creator = ServicesIntegrationResponseFactory::make($sale->services->first(), $response);

        $encodedServiceTransaction = base64_encode($service->serviceTransaction);
        $remoteSaleUrl             = "{$urlOrigin}/pagamento/{$encodedServiceTransaction}";

        self::assertEquals($creator->getData()->messages, 'Venda salva com sucesso');
        self::assertEquals($creator->getData()->urlOrigin, 'http://localhost:8081');
        self::assertEquals($creator->getData()->remoteSaleUrl, $remoteSaleUrl);

        $this->assertDatabaseHas('sales', [
            'services.remoteSale' => true,
            'services.serviceTransaction'  => $service->serviceTransaction,
            'services.integratorPaymentURL' => 'https://homologacao.tradeapp.com.br/pagamento/018579a0-ad01',
            'services.paymentUrl'  => $remoteSaleUrl,
        ], 'mongodb');
    }
}
