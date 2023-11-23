<?php

namespace VivoBR\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;
use VivoBR\Adapters\Response\VivoBRSaleResponse;
use VivoBR\Tests\Helpers\VivoFactoriesHelper;
use function GuzzleHttp\Psr7\stream_for;

class VivoBrSaleResponseTest extends TestCase
{
    use VivoFactoriesHelper;

    /** @test */
    public function should_return_false_when_credit_analysis_contains_error(): void
    {
        $response = $this->buildResponse(__DIR__ . '/../../ServerTest/responses/sales/failureAnaliseCredito.json');

        $this->assertTrue($response->isCreditAnalysis());
        $this->assertFalse($response->isCreditAnalysisSuccess());
    }

    /** @test */
    public function should_return_true_when_credit_analysis_success(): void
    {
        $response = $this->buildResponse(__DIR__ . '/../../ServerTest/responses/sales/successSale.json');

        $this->assertTrue($response->isCreditAnalysis());
        $this->assertTrue($response->isCreditAnalysisSuccess());
    }

    /** @test */
    public function should_return_true_when_not_exists_credit_analysis(): void
    {
        $response = $this->buildResponse();

        $this->assertFalse($response->isCreditAnalysis());
        $this->assertTrue($response->isCreditAnalysisSuccess());
    }

    /** @test */
    public function should_return_correct_identifiers(): void
    {
        $response = $this->buildResponse(__DIR__ . '/../../ServerTest/responses/sales/successSale.json');

        $this->assertEquals([
            'idVenda' =>  'SP-395569',
            'idServico' => 405826
        ], $response->getIdentifiers());
    }

    /** @test */
    public function should_return_true_when_codigo_is_0(): void
    {
        $response = $this->buildResponse(__DIR__ . '/../../ServerTest/responses/sales/successSale.json');

        $this->assertTrue($response->isSuccess());
    }

    /** @test */
    public function should_return_true_when_success_pre_activation(): void
    {
        $response = $this->buildResponse(__DIR__ . '/../../ServerTest/responses/sales/preActivationApproved.json');

        $this->assertTrue($response->isSuccess());
    }

    /** @test */
    public function should_return_false_when_pre_activation_reproved_iccid(): void
    {
        $response = $this->buildResponse(__DIR__ . '/../../ServerTest/responses/sales/preActivationReprovedIccid.json');

        $this->assertFalse($response->isSuccess());
    }

    /** @test */
    public function should_return_false_when_codigo_is_not_0(): void
    {
        $response = $this->buildResponse(__DIR__ . '/../../ServerTest/responses/sales/failureUserNotFound.json');

        $this->assertFalse($response->isSuccess());
    }

    /** @test */
    public function should_return_false_when_codigo_analiseCredito_failure(): void
    {
        $response = $this->buildResponse(__DIR__ . '/../../ServerTest/responses/sales/failureAnaliseCredito.json');

        $this->assertFalse($response->isSuccess());
    }

    /** @test */
    public function should_return_correct_adapted_when_is_with_success(): void
    {
        $response = $this->buildResponse(__DIR__ . '/../../ServerTest/responses/sales/successSale.json');

        $adapt = $response->getAdapted();

        $this->assertTrue(array_key_exists('transportedMessage', $adapt));
        $this->assertTrue(array_key_exists('pid', $adapt));
        $this->assertTrue(array_key_exists('data', $adapt));
    }

    /** @test */
    public function should_return_correct_adapted_when_is_with_errors(): void
    {
        $response = $this->buildResponse(__DIR__ . '/../../ServerTest/responses/sales/failureUserNotFound.json');

        $adapt = $response->getAdapted();

        $this->assertTrue(array_key_exists('message', $adapt));
        $this->assertTrue(array_key_exists('data', $adapt));
    }

    /** @test */
    public function should_return_correct_message(): void
    {
        $data = [
            'detalhes' => [
               'detalhe1'
            ],
            'analiseCredito' => [
                'mensagem' => 'analise1'
            ],
            'mensagem' => 'msg1'
        ];

        $response = $this->buildResponse(null, $data);
        $this->assertEquals('detalhe1', $response->getMessage());

        unset($data['detalhes']);
        $response = $this->buildResponse(null, $data);
        $this->assertEquals('analise1', $response->getMessage());

        unset($data['analiseCredito']);
        $response = $this->buildResponse(null, $data);
        $this->assertEquals('msg1', $response->getMessage());
    }

    /** @test */
    public function should_ignore_only_cod_errors(): void
    {
        foreach (VivoBRSaleResponse::COD_ERRORS as $code) {
            $response = $this->buildResponse(null, [
                'codigo' => $code
            ]);

            $this->assertFalse($response->shouldIgnoreErrors());

            $response = $this->buildResponse(null, [
                'codigo'         => 12, //FAKE
                'analiseCredito' => [
                    'codigo' => $code
                ]
            ]);

            $this->assertFalse($response->shouldIgnoreErrors());
        }

        $response = $this->buildResponse(null, [
            'codigo' => 9213 //code false
        ]);
        $this->assertTrue($response->shouldIgnoreErrors());

        $response = $this->buildResponse(null, [
            'analiseCredito' => [
                'codigo' => 123 //fake
            ]
        ]);
        $this->assertTrue($response->shouldIgnoreErrors());
    }

    private function buildResponse(string $dir = null, array $data = []): VivoBRSaleResponse
    {
        $json           = $dir ? file_get_contents($dir) : json_encode($data);
        $guzzleResponse = new Response(200, [], stream_for($json));
        $response       = RestResponse::success($guzzleResponse);

        return VivoBRSaleResponse::make($response);
    }
}
