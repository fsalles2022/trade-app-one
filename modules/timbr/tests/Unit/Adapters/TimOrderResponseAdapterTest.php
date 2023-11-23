<?php

namespace TimBR\Tests\Unit\Adapters;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TimBR\Adapters\TimOrderResponseAdapter;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class TimOrderResponseAdapterTest extends TestCase
{
    /** @test */
    public function should_return_error_format_when_tim_return_error()
    {
        $stream = stream_for('{
                    "type" : "error", 
                    "status" : "406", 
                    "internalCode" : "-2001", 
                    "message" : "Erro na validacao de DDD x UF", 
                    "transactionId" : "Id-61eb865b8b47b7fccf4db303"
                }');

        $mockResponse = new Response(200, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new TimOrderResponseAdapter($response);
        $response     = $adapted->getAdapted();

        self::assertArrayHasKey(0, $response['errors']);
        self::assertArrayHasKey('message', $response['errors'][0]);
    }

    /** @test */
    public function should_return_error_format_and_message_when_tim_return_error()
    {
        $assertMessage = 'Erro na validacao de DDD x UF';
        $stream        = stream_for('{
                    "type" : "error", 
                    "status" : "406", 
                    "internalCode" : "-2001", 
                    "message" : "Erro na validacao de DDD x UF", 
                    "transactionId" : "Id-61eb865b8b47b7fccf4db303"
                }');
        $mockResponse  = new Response(500, [], $stream);
        $mockError     = new BadResponseException('', new Request('', ''), $mockResponse);
        $response      = RestResponse::failure($mockError);
        $adapted       = new TimOrderResponseAdapter($response);
        $response      = $adapted->getAdapted();
        $status        = $adapted->getStatus();
        self::assertEquals(\Illuminate\Http\Response::HTTP_PRECONDITION_FAILED, $status);
        self::assertEquals($assertMessage, $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_error_format_and_message_when_tim_return_error_500()
    {
        $assertMessage = 'Nao foi possivel realizar a ativacao do numero neste chip. Tente novamente com um novo chip';
        $stream        = stream_for('{
                    "type" : "error", 
                    "status" : "500", 
                    "internalCode" : "-31008", 
                    "message" : "' . $assertMessage . '", 
                    "transactionId" : "Id-891e885b9539bb833c351a1b"
                }');
        $mockResponse  = new Response(500, [], $stream);
        $mockError     = new BadResponseException('', new Request('', ''), $mockResponse);
        $response      = RestResponse::failure($mockError);
        $adapted       = new TimOrderResponseAdapter($response);
        $response      = $adapted->getAdapted();
        $status        = $adapted->getStatus();
        self::assertEquals(\Illuminate\Http\Response::HTTP_PRECONDITION_FAILED, $status);
        self::assertEquals($assertMessage, $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_error_format_and_message_when_tim_return_error_406()
    {
        $assertMessage = 'Nao foi possivel realizar a ativacao do numero neste chip. Tente novamente com um novo chip';
        $stream        = stream_for('{
                    "type" : "error", 
                    "status" : "406", 
                    "internalCode" : "-31008", 
                    "message" :  "' . $assertMessage . '", 
                    "transactionId" : "Id-891e885b9539bb833c351a1b"
                }');
        $mockResponse  = new Response(406, [], $stream);
        $mockError     = new ClientException('', new Request('', ''), $mockResponse);
        $response      = RestResponse::failure($mockError);
        $adapted       = new TimOrderResponseAdapter($response);
        $response      = $adapted->getAdapted();
        $status        = $adapted->getStatus();
        self::assertEquals(\Illuminate\Http\Response::HTTP_PRECONDITION_FAILED, $status);
        self::assertEquals($assertMessage, $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_error_format_and_message_when_tim_return_server_erro406()
    {
        $assertMessage = 'Nao foi possivel realizar a ativacao do numero neste chip. Tente novamente com um novo chip';
        $stream        = stream_for('{
                    "type" : "error", 
                    "status" : "500", 
                    "internalCode" : "-31008", 
                    "message" : "Nao foi possivel realizar a ativacao do numero neste chip. Tente novamente com um novo chip", 
                    "transactionId" : "Id-891e885b9539bb833c351a1b"
                }');
        $mockResponse  = new Response(406, [], $stream);
        $mockError     = new ServerException('', new Request('', ''), $mockResponse);
        $response      = RestResponse::failure($mockError);
        $adapted       = new TimOrderResponseAdapter($response);
        $response      = $adapted->getAdapted();
        $status        = $adapted->getStatus();
        self::assertEquals(\Illuminate\Http\Response::HTTP_PRECONDITION_FAILED, $status);
        self::assertEquals($assertMessage, $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_sale_and_message_when_tim_return_succes()
    {
        $stream       = stream_for('{
                    "order" : {
                    "protocol" : "111",
                    "contract" : {
                    "msisdn" : "123123"
                    }
                    }
                }');
        $mockResponse = new Response(200, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new TimOrderResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertArrayHasKey('order', $response);
    }
}
