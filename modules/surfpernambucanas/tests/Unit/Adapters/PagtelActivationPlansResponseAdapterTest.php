<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use SurfPernambucanas\Adapters\PagtelActivationPlansResponseAdapter;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class PagtelActivationPlansResponseAdapterTest extends TestCase
{
    public function test_should_get_adapted_response(): void
    {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            $this->getBody()
        );

        $adapter = new PagtelActivationPlansResponseAdapter(RestResponse::success($responseMock));

        $responseAdapted = $adapter->getAdapted();

        $this->assertEquals(null, data_get($responseAdapted, 'code'));
        $this->assertEquals('Listagem de planos', data_get($responseAdapted, 'msg'));
        $this->assertEquals($this->getExpectedPlansData(), data_get($responseAdapted, 'plans'));
        $this->assertEquals(HttpResponse::HTTP_OK, $adapter->getStatus());
    }

    protected function getBody(): string
    {
        return '{
            "message": "Listagem de planos", 
            "payload": [{
                "plan_id": "8B7880FD-9B17-419D-9EDB-A0EC1E69C1C2",
                "name": "Pernambucanas Conectado",
                "value": "2500",
                "validity": "30 dias ",
                "type": "connected-plan",
                "type_description": "PLANO CONECTADO",
                "advantages": [{
                    "title": "1,5 GB de internet",
                    "description": "de internet na maior rede 4G do país",
                    "alias": "internet",
                    "type": "advantage"
                }]
            }]
        }';
    }

    /** @return array[] */
    protected function getExpectedPlansData(): array
    {
        return [
            [
                "id"                => "8B7880FD-9B17-419D-9EDB-A0EC1E69C1C2",
                "label"             => "Pernambucanas Conectado",
                "price"             => 25.00,
                "validity"          => "30 dias ",
                "type"              => "connected-plan",
                "type_description"  => "PLANO CONECTADO",
                "advantages"        => [
                    [
                        "label"         => "1,5 GB de internet",
                        "description"   => "de internet na maior rede 4G do país",
                        "alias"         => "internet",
                    ]
                ]
            ]
        ];
    }

    public function test_should_get_adapted_response_with_message_error(): void
    {
        $responseMock = new Response(
            HttpResponse::HTTP_UNAUTHORIZED,
            [],
            '{
                "message": "unauthorized error",
                "payload": [],
                "error": [{"message": "Autenticação inválida", "param": "token"}]
            }'
        );

        $adapter = new PagtelActivationPlansResponseAdapter(RestResponse::success($responseMock));

        $responseAdapted = $adapter->getAdapted();

        $this->assertEquals(null, data_get($responseAdapted, 'code'));
        $this->assertEquals('Autenticação inválida', data_get($responseAdapted, 'msg'));
        $this->assertEquals([], data_get($responseAdapted, 'plans'));
        $this->assertEquals(HttpResponse::HTTP_CONFLICT, $adapter->getStatus());
    }
}
