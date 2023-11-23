<?php

namespace TradeAppOne\Tests\Unit\Domain\Factories;

use ClaroBR\Tests\Helpers\ClaroServices;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Factories\ServicesIntegrationResponseFactory;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Tests\TestCase;

class ServicesIntegrationResponseFactoryTest extends TestCase
{
    /** @test */
    public function should_success_save_sale_response(): void
    {
        $sale = factory(Sale::class)->create([
            'services' => [ClaroServices::ControleBoleto()->toArray()]
        ]);

        $response = response()->json([
            'messages' => trans('messages.sale_saved')
        ], Response::HTTP_OK);

        $creator = ServicesIntegrationResponseFactory::make($sale->services->first(), $response);

        self::assertEquals('Venda salva com sucesso', $creator->getData()->messages);
    }
}
