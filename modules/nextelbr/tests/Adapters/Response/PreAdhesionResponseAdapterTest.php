<?php

namespace NextelBR\Tests\Adapters\Response;

use GuzzleHttp\Psr7\Response;
use NextelBR\Adapters\Response\PreAdhesionResponseAdapter;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class PreAdhesionResponseAdapterTest extends TestCase
{
    /** @test */
    public function should_return_with_message_when_exists()
    {
        $stream       = stream_for("{\"codigo\":3000,\"subCodigo\":200,\"mensagem\":\"Ocorreu um erro ao consultar o perfil do cliente, por favor repita a etapa anterior.\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new PreAdhesionResponseAdapter($response);
        $response     = $adapted->getAdapted();

        self::assertEquals(trans('nextelBR::messages.3000.200'), $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_default_message_when_exists_code_no_exists()
    {
        $stream       = stream_for("{\"codigo\":8888,\"subCodigo\":200,\"mensagem\":\"Mensagem Original.\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new PreAdhesionResponseAdapter($response);
        $response     = $adapted->getAdapted();

        self::assertEquals('Mensagem Original.', $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_original_message_if_trans_for_sub_code_no_exists()
    {
        $stream       = stream_for("{\"codigo\":1000,\"subCodigo\":222,\"mensagem\":\"Mensagem Original.\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new PreAdhesionResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals('Mensagem Original.', $response['errors'][0]['message']);
    }
}
