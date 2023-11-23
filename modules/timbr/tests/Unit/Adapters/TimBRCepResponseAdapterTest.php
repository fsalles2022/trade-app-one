<?php

namespace TimBR\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
use TimBR\Adapters\TimBRCepResponseAdapter;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class TimBRCepResponseAdapterTest extends TestCase
{
    /** @test */
    public function should_return_when_cep_not_found()
    {
        $stream       = stream_for("{    \"type\": \"error\",    \"status\": \"500\",    \"internalCode\": \"-2001\",    \"message\": \"Registro nao encontrado\",    \"transactionId\": \"Id-bc55095c19e8386530ab0e07\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new TimBRCepResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('timBR::messages.cep.not_found'), $response['errors'][0]['message']);
    }
}
