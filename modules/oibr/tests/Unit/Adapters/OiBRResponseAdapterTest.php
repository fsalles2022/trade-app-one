<?php

namespace OiBR\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use OiBR\Adapters\OiBRResponseAdapter;
use OiBR\Enumerators\OiBRBusinessCodes;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class OiBRResponseAdapterTest extends TestCase
{
    /** @test */
    public function should_return_msisdn_block()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"MSISDN_BLOQUEADO\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.MSISDN_BLOQUEADO'), $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_integracao_indisponivel()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"INTEGRACAO_INDISPONIVEL\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        $status       = $adapted->getStatus();
        self::assertEquals(trans('oiBR::messages.INTEGRACAO_INDISPONIVEL'), $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_integracao_indisponivel_406()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::ASSINANTE_INVALIDO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        $status       = $adapted->getStatus();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::ASSINANTE_INVALIDO), $response['errors'][0]['message']);
        self::assertEquals($status, \Illuminate\Http\Response::HTTP_NOT_ACCEPTABLE);
    }
   /** @test */
    public function should_return_vendedor_invalido()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::VENDEDOR_INVALIDO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::VENDEDOR_INVALIDO), $response['errors'][0]['message']);
    }

     /** @test */
    public function should_return_pdv_inexistente()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::PDV_INEXISTENTE . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::PDV_INEXISTENTE), $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_pdv_inativo()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::PDV_INATIVO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::PDV_INATIVO), $response['errors'][0]['message']);
    }

     /** @test */
    public function should_return_oferta_nao_encontrada()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::OFERTA_NAO_ENCONTRADA . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::OFERTA_NAO_ENCONTRADA), $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_oferta_invalida()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::OFERTA_INVALIDA . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::OFERTA_INVALIDA), $response['errors'][0]['message']);
    }

     /** @test */
    public function should_return_msisdn_nao_disponivel()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::MSISDN_NAO_DISPONIVEL . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::MSISDN_NAO_DISPONIVEL), $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_msisdn_invalido()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::MSISDN_INVALIDO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::MSISDN_INVALIDO), $response['errors'][0]['message']);
    }

     /** @test */
    public function should_return_estabelecimento_invalido()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::ESTABELECIMENTO_INVALIDO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::ESTABELECIMENTO_INVALIDO), $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_erro_habilitar_monitoramento_recarga()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::ERRO_HABILITAR_MONITORAMENTO_RECARGA . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::ERRO_HABILITAR_MONITORAMENTO_RECARGA), $response['errors'][0]['message']);
    }

     /** @test */
    public function should_return_email_invalido()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::EMAIL_INVALIDO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::EMAIL_INVALIDO), $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_ddd_invalido()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::DDD_INVALIDO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::DDD_INVALIDO), $response['errors'][0]['message']);
    }

     /** @test */
    public function should_return_cpf_invalido()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::CPF_INVALIDO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::CPF_INVALIDO), $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_contato_invalido()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::CONTATO_INVALIDO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::CONTATO_INVALIDO), $response['errors'][0]['message']);
    }


    /** @test */
    public function should_return_cliente_nao_encontrado()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::CLIENTE_NAO_ENCONTRADO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::CLIENTE_NAO_ENCONTRADO), $response['errors'][0]['message']);
    }

      /** @test */
    public function should_return_cep_invalido()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::CEP_INVALIDO . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::CEP_INVALIDO), $response['errors'][0]['message']);
    }

       /** @test */
    public function should_venda_ddd_invalido()
    {
        $stream       = stream_for("{\"erros\":[{\"mensagem\":\"" . OiBRBusinessCodes::VENDA_DDD_INVALIDA . "\"}],\"ref\":\"51c78366ecfb849e\"}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('oiBR::messages.' . OiBRBusinessCodes::VENDA_DDD_INVALIDA), $response['errors'][0]['message']);
    }
    /** @test */
    public function should_return_success_201()
    {
        $stream       = stream_for("{}");
        $mockResponse = new Response(201, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('messages.default_success'), $response['message']);
    }

    /** @test */
    public function should_return_success_200()
    {
        $stream       = stream_for("{}");
        $mockResponse = new Response(200, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new OiBRResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('messages.default_success'), $response['message']);
    }
}
