<?php

namespace ClaroBR\Tests\Unit\Adapters;

use ClaroBR\Adapters\CreditAnalysisResponseAdapter;
use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class CreditAnalysisResponseAdapterTest extends TestCase
{
    /** @test */
    public function should_return_no_score()
    {
        $stream       = stream_for("{\"type\":\"success\",\"message\":\"Analise de Cr\u00e9dito realizado com sucesso\",\"data\":{\"credit\":0,\"products\":[],\"customer\":{\"cidade_ibge\":\"5002704\",\"cidade_ddd\":\"11\",\"uf_ibge\":\"SP\",\"cpf\":\"01228305471\",\"data_nascimento\":\"1983-12-16\",\"nome\":\"EMMANUELLE CRISTIANY DA SILVA AVELINO\",\"cep\":\"59127010\",\"filiacao\":\"MARIA MARGARIDA DA SILVA\",\"marital_status\":null,\"occupation\":null,\"salary_range\":null}}}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new CreditAnalysisResponseAdapter($response);
        $response     = $adapted->getAdapted();
        $status       = $adapted->getStatus();
        self::assertEquals(trans('siv::messages.score.no_score'), $response['errors'][0]['message']);
        self::assertEquals(\Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY, $status);
    }

    /** @test */
    public function should_return_score()
    {
        $stream       = stream_for("{\"type\":\"success\",\"message\":\"Analise de Cr\u00e9dito realizado com sucesso\",\"data\":{\"credit\":450,\"products\":[],\"customer\":{\"cidade_ibge\":\"5002704\",\"cidade_ddd\":\"11\",\"uf_ibge\":\"SP\",\"cpf\":\"01228305471\",\"data_nascimento\":\"1983-12-16\",\"nome\":\"EMMANUELLE CRISTIANY DA SILVA AVELINO\",\"cep\":\"59127010\",\"filiacao\":\"MARIA MARGARIDA DA SILVA\",\"marital_status\":null,\"occupation\":null,\"salary_range\":null}}}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new CreditAnalysisResponseAdapter($response);
        $response     = $adapted->getAdapted();
        $status       = $adapted->getStatus();
        self::assertEquals(450, $response['data']['credit']);
        self::assertEquals(\Illuminate\Http\Response::HTTP_OK, $status);
    }
}
