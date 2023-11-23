<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use SurfPernambucanas\Adapters\PagtelAllocatedMsisdnResponseAdapter;
use SurfPernambucanas\Enumerators\PagtelResponseCode;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class PagtelAllocatedMsisdnResponseAdapterTest extends TestCase
{
    public function test_should_get_adapted_response(): void
    {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            '{"code": "' . PagtelResponseCode::SUCCESS . '", "msg": "Sucesso", "msisdn": "5511999998888"}'
        );

        $adapter = new PagtelAllocatedMsisdnResponseAdapter(RestResponse::success($responseMock));

        $responseAdapted = $adapter->getAdapted();

        $this->assertEquals(PagtelResponseCode::SUCCESS, data_get($responseAdapted, 'code'));
        $this->assertEquals('Sucesso', data_get($responseAdapted, 'msg'));
        $this->assertEquals('5511999998888', data_get($responseAdapted, 'msisdn'));
    }
}
