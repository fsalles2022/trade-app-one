<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use SurfPernambucanas\Adapters\PagtelPlansResponseAdapter;
use SurfPernambucanas\Enumerators\PagtelResponseCode;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class PagtelPlansResponseAdapterTest extends TestCase
{
    public function test_should_get_adapted_response(): void
    {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            '{"code": "' . PagtelResponseCode::SUCCESS . '", "msg": "Sucesso", "valueList": [{"value": 2990, "note": "PlanoTeste"}]}'
        );

        $adapter = new PagtelPlansResponseAdapter(RestResponse::success($responseMock));

        $responseAdapted = $adapter->getAdapted();

        $this->assertEquals(PagtelResponseCode::SUCCESS, data_get($responseAdapted, 'code'));
        $this->assertEquals('Sucesso', data_get($responseAdapted, 'msg'));
        $this->assertEquals([['price' => 29.90, 'label' => 'PlanoTeste']], data_get($responseAdapted, 'plans'));
    }
}
