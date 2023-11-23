<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use SurfPernambucanas\Adapters\PagtelAddCardResponseAdapter;
use SurfPernambucanas\Enumerators\PagtelResponseCode;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class PagtelAddCardResponseAdapterTest extends TestCase
{
    public function test_should_get_adapted_response(): void
    {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            '{"code": "' . PagtelResponseCode::SUCCESS . '", "msg": "Sucesso", "paymentID": "4"}'
        );

        $adapter = new PagtelAddCardResponseAdapter(RestResponse::success($responseMock));

        $responseAdapted = $adapter->getAdapted();

        $this->assertEquals(PagtelResponseCode::SUCCESS, data_get($responseAdapted, 'code'));
        $this->assertEquals('Sucesso', data_get($responseAdapted, 'msg'));
        $this->assertEquals('4', data_get($responseAdapted, 'paymentId'));
    }
}
