<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use SurfPernambucanas\Adapters\PagtelActivationActivateResponseAdapter;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class PagtelActivationActivateResponseAdapterTest extends TestCase
{
    public function test_should_get_adapted_response(): void
    {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            $this->getBody()
        );

        $adapter = new PagtelActivationActivateResponseAdapter(RestResponse::success($responseMock));

        $responseAdapted = $adapter->getAdapted();

        $this->assertEquals(null, data_get($responseAdapted, 'code'));
        $this->assertEquals('Ativado', data_get($responseAdapted, 'msg'));
        $this->assertEquals(HttpResponse::HTTP_OK, $adapter->getStatus());
    }

    protected function getBody(): string
    {
        return '{
            "message": "Ativado", 
            "payload": {
                "activationId": "123456",
                "iccid": "8955170110240000347",
                "msisdn": "5511999998888"
            }
        }';
    }

    public function test_should_get_adapted_response_with_message_error(): void
    {
        $responseMock = new Response(
            HttpResponse::HTTP_CONFLICT,
            [],
            '{
                "message": "onflict error",
                "payload": {},
                "error": [{"message": "O iccid se encontra em uso", "param": "iccid"}]
            }'
        );

        $adapter = new PagtelActivationActivateResponseAdapter(RestResponse::success($responseMock));

        $responseAdapted = $adapter->getAdapted();

        $this->assertEquals(null, data_get($responseAdapted, 'code'));
        $this->assertEquals('O iccid se encontra em uso', data_get($responseAdapted, 'msg'));
        $this->assertEquals(HttpResponse::HTTP_CONFLICT, $adapter->getStatus());
    }
}
