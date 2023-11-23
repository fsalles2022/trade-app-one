<?php

namespace TimBR\Tests\Unit\Adapters;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TimBR\Adapters\TimEligibilityResponseAdapter;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class TimEligibilityResponseAdapterTest extends TestCase
{
    /** @test */
    public function should_return_error_customer_format()
    {
        $assertMessage = 'Perfil Pré Pago';
        $stream        = stream_for('{
                    "type" : "error", 
                    "status" : "406", 
                    "internalCode" : "-10001", 
                    "message" : "Perfil Pré Pago", 
                    "transactionId" : "Id-61eb865b8b47b7fccf4db303"
                }');
        $mockResponse  = new Response(500, [], $stream);
        $mockError     = new BadResponseException('', new Request('', ''), $mockResponse);
        $response      = RestResponse::failure($mockError);
        $adapted       = new TimEligibilityResponseAdapter($response, Operations::TIM_CONTROLE_FATURA);
        $response      = $adapted->getAdapted();
        $status        = $adapted->getStatus();
        self::assertEquals(\Illuminate\Http\Response::HTTP_NOT_ACCEPTABLE, $status);
        self::assertEquals($assertMessage, $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_error_default()
    {
        $assertMessage = 'Nao foi possivel realizar o processamento. Tente novamente mais tarde';
        $stream        = stream_for('{ 
        "type": "error", "status": "500", 
        "internalCode": "-2001", 
        "message": "Nao foi possivel realizar o processamento. Tente novamente mais tarde", 
        "transactionId": "Id-86730a5c894f8243f7dfbd24"}');
        $mockResponse  = new Response(500, [], $stream);
        $mockError     = new BadResponseException('', new Request('', ''), $mockResponse);
        $response      = RestResponse::failure($mockError);
        $adapted       = new TimEligibilityResponseAdapter($response, Operations::TIM_CONTROLE_FATURA);
        $response      = $adapted->getAdapted();
        $status        = $adapted->getStatus();
        self::assertEquals(\Illuminate\Http\Response::HTTP_PRECONDITION_FAILED, $status);
        self::assertEquals($assertMessage, $response['errors'][0]['message']);
    }
}
