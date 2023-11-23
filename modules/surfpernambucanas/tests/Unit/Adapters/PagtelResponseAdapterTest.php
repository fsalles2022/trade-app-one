<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use SurfPernambucanas\Adapters\PagtelResponseAdapter;
use SurfPernambucanas\Enumerators\PagtelResponseCode;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class PagtelResponseAdapterTest extends TestCase
{
    public function test_should_get_adapted_response(): void
    {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            '{"code": "' . PagtelResponseCode::SUCCESS . '", "msg": "Registro realizado com sucesso."}'
        );

        $adapter = new PagtelResponseAdapter(RestResponse::success($responseMock));

        $responseAdapted = $adapter->getAdapted();

        $this->assertEquals(PagtelResponseCode::SUCCESS, data_get($responseAdapted, 'code'));
        $this->assertEquals('Registro realizado com sucesso.', data_get($responseAdapted, 'msg'));
    }
}
