<?php

namespace OiBR\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use OiBR\Adapters\OiBRControleCartaoEligibilityResponseAdapter;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class OiBRControleCartaoEligibilityResponseAdapterTest extends TestCase
{
    /** @test */
    public function disponivel()
    {
        $stream       = stream_for("{
\"code\": \"0\",
\"message\": \"Operacao efetuada com sucesso.\",
\"ref\": \"AC02BA0C56AD40B089ADD0A4CE357D47\", \"result\": {
\"status\": \"AVAILABLE\" }
}");
        $mockResponse = new Response(200, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRControleCartaoEligibilityResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals('AVAILABLE', $response['status']);
    }
}
