<?php

namespace VivoBR\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;
use VivoBR\Adapters\SunResponseAdapter;
use function GuzzleHttp\Psr7\stream_for;

class SunResponseAdapterTest extends TestCase
{
    /** @test */
    public function should_return_nested_data()
    {
        $stream       = stream_for("{\"codigo\":0,\"idVenda\":\"RS-242498\",\"servicos\":[{\"id\":276996}],\"urlM4U\":\"https:\/\/vivocontrole.stg.m4u.com.br\/varejo?k=m7wgOd01br4IOtk1PL%2BcJOSRe6EAuCcN4Kg6JYfs%2BqA%3D&canal=7nZ%2BCJtlRtyuwfWxToGoyQ%3D%3D&msisdn=FPcYsWRBzlxZUdX8xOJArg%3D%3D&cpf=8dZHlo7GBrfvFbWUA3rHSQ%3D%3D&plano=ybuOxHGaaMM%2BI4GegJs7E9yM3yKLjkbcnsEsiuqU9r4%3D&cpfPr=Nvop7SANB40dhH3sU6EO2g%3D%3D&cnpjPr=g4muvRIztBFIkdoh8nN8oA%3D%3D&parentUrl=g7eFkQ1oR7Y%3D&externalId=1jRI%2FBLkCvovMzS55%2FTv9oHwi6Uxuaxr4qYBEcwwXQA%3D\",\"urlM4UPlana\":\"https:\/\/vivocontrole.stg.m4u.com.br\/varejo?k=30702A005B3069F250CF0B22CC2&canal=varejo-web&msisdn=11998989989&cpf=00000009652&plano=vivo_ctrl_cartao_1_5gb_50min&cpfPr=02722024012&cnpjPr=RS2283-65&parentUrl=*&externalId=e4dedad75d44a99fadbc5cdaea274d11\"}");
        $mockResponse = new Response(200, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new SunResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertArrayHasKey('data', $response);
    }

    /** @test */
    public function should_return_message()
    {
        $assertMessage = 'Já existe uma venda (SP-411122) registrada com os dados informados.';
        $stream        = stream_for("{
                    \"codigo\" : 1100, 
                    \"mensagem\" : \"{$assertMessage}\", 
                    \"detalhes\" : null
                }");
        $mockResponse  = new Response(200, [], $stream);
        $response      = RestResponse::success($mockResponse);
        $adapted       = new SunResponseAdapter($response);
        $response      = $adapted->getAdapted();
        self::assertArrayHasKey('message', $response['errors'][0]);
        self::assertEquals($assertMessage, $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_with_status_421()
    {
        $assertMessage = 'Já existe uma venda (SP-411122) registrada com os dados informados.';
        $stream        = stream_for("{
                    \"codigo\" : 1100, 
                    \"mensagem\" : \"{$assertMessage}\", 
                    \"detalhes\" : null
                }");
        $mockResponse  = new Response(200, [], $stream);
        $response      = RestResponse::success($mockResponse);
        $adapted       = new SunResponseAdapter($response);
        $response      = $adapted->getStatus();
        self::assertEquals(\Illuminate\Http\Response::HTTP_PRECONDITION_FAILED, $response);
    }

    /** @test */
    public function should_return_no_message_use_default()
    {
        $assertMessage = 'Detalhe';
        $stream        = stream_for("{
                    \"codigo\" : 1100, 
                    \"mensagem\" : null, 
                    \"detalhes\" : [\"{$assertMessage}\"]
                }");
        $mockResponse  = new Response(200, [], $stream);
        $response      = RestResponse::success($mockResponse);
        $adapted       = new SunResponseAdapter($response);
        $response      = $adapted->getAdapted();
        $status        = $adapted->getStatus();
        self::assertEquals($assertMessage, $response['errors'][0]['message']);
        self::assertEquals(\Illuminate\Http\Response::HTTP_PRECONDITION_FAILED, $status);
    }
}
