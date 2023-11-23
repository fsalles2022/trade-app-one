<?php

namespace ClaroBR\Tests\Unit\Adapters;

use ClaroBR\Adapters\SivResponseAdapter;
use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class SivResponseAdapterTest extends TestCase
{
    /** @test */
    public function should_return_msisdn_block()
    {
        $stream       = stream_for("{\"type\":\"error\",\"message\":\"Os parâmetros [produto] são obrigatórios\",\"data\":[],\"content\":{\"tipo\":\"RequisicaoInvalidaParaSVC\",\"motivo\":\"Requisi\u00e7\u00e3o inv\u00e1lida para uri Venda\/v2\/venda\/cliente\/cadastro\/identificar\",\"mensagem\":\"Os par\u00e2metros [produto] s\u00e3o obrigat\u00f3rios\",\"descricao\":\"Os par\u00e2metros [produto] s\u00e3o obrigat\u00f3rios\",\"direcionamento\":\"Verifique se todos os par\u00e2metros de entrada esperados foram informados e se os valores preenchidos est\u00e3o corretos e s\u00e3o compat\u00edveis com o dom\u00ednio esperado\"}}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new SivResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('siv::messages.NUMERO_NAO_ATIVO'), $response['errors'][0]['message']);
    }

    /** @test */
    public function should_return_msisdn_blk()
    {
        $stream       = stream_for("{\"type\":\"error\",\"message\":\"Os parâmetros [produto] são os\",\"data\":[],\"content\":{\"tipo\":\"RequisicaoInvalidaParaSVC\",\"motivo\":\"Requisi\u00e7\u00e3o inv\u00e1lida para uri Venda\/v2\/venda\/cliente\/cadastro\/identificar\",\"mensagem\":\"Os par\u00e2metros [produto] s\u00e3o obrigat\u00f3rios\",\"descricao\":\"Os par\u00e2metros [produto] s\u00e3o obrigat\u00f3rios\",\"direcionamento\":\"Verifique se as políticas de uso para a operação solicitada\"}}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new SivResponseAdapter($response);
        $response     = $adapted->getAdapted();
        self::assertEquals(trans('siv::messages.NUMERO_NAO_ATIVO'), $response['errors'][0]['message']);
    }
    
    /** @test */
    public function must_return_status_206_when_sim_card_is_invalid()
    {
        $stream       = stream_for("{\"type\":\"error\",\"message\":\"Simcard inv\u00e1lido.\",\"data\":[],\"content\":{\"tipo\":\"SimCardInvalido\",\"motivo\":\"O simcard informado n\u00e3o foi encontrado.\",\"mensagem\":\"Simcard inv\u00e1lido.\",\"descricao\":null,\"direcionamento\":\"Verifique se o iccid \u00e9 valido ou se um bilhete de portabilidade j\u00e1 existe para este n\u00famero\"}}");
        $mockResponse = new Response(400, [], $stream);
        $response     = RestResponse::success($mockResponse);
        $adapted      = new SivResponseAdapter($response);

        $this->assertEquals(206, $adapted->getStatus());
        $this->assertEquals("Simcard inválido.", $adapted->getAdapted()['errors'][0]['message']);
    }
}
