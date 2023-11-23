<?php

namespace OiBR\Tests\Unit\Services;

use Illuminate\Http\Response;
use OiBR\Assistance\OiControleCartaoAssistance;
use OiBR\Connection\OiBRConnection;
use OiBR\Models\OiBRControleCartao;
use OiBR\Tests\Helpers\OiBRFactories;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\Helpers\Builders\RestResponseBuilder;
use TradeAppOne\Tests\TestCase;

class OiControleCartaoAssistanceTest extends TestCase
{
    use OiBRFactories;

    /** @test */
    public function should_accept_activation_when_oi_server_success()
    {
        $oiActivationResponse       = (new RestResponseBuilder())->withBodyFromArray([])->withStatus(202)->success();
        $oiStatusActivationResponse = (new RestResponseBuilder()
        )->withBodyFromFile(__DIR__ . '/../../ServerTest/responses/activation/queryStatusCartaoActivation.json')
            ->withStatus(202)->success();
        $oiConnection               = \Mockery::mock(OiBRConnection::class)->makePartial();
        $oiConnection->shouldReceive('controleCartaoSale')->andReturn($oiActivationResponse);
        $oiConnection->shouldReceive('controleCartaoStatus')->andReturn($oiStatusActivationResponse);

        $assistance = new OiControleCartaoAssistance(resolve(SaleService::class), $oiConnection);
        $result     = $assistance->integrateService($this->serviceInstance()->services[0]);
        self::assertEquals($result->status(), Response::HTTP_OK);
    }

    protected function serviceInstance(): Sale
    {
        $pointOfSale = $this->pointOfSaleOiBR()->toArray();
        $service     = $this->oiBRfactory()->of(OiBRControleCartao::class)->make()->toArray();
        return factory(Sale::class)->make(['services' => [$service], 'pointOfSale' => $pointOfSale]);
    }

    /** @test */
    public function should_refuse_activation_when_oi_server_failure()
    {
        $oiActivationResponse       = (new RestResponseBuilder())->withBodyFromArray([])->withStatus(202)->success();
        $oiStatusActivationResponse = (new RestResponseBuilder()
        )->withBodyFromArray(['status' => 'FALHA'])
            ->withStatus(400)->failure();
        $oiConnection               = \Mockery::mock(OiBRConnection::class)->makePartial();
        $oiConnection->shouldReceive('controleCartaoSale')->andReturn($oiActivationResponse);
        $oiConnection->shouldReceive('controleCartaoStatus')->andReturn($oiStatusActivationResponse);

        $assistance = new OiControleCartaoAssistance(resolve(SaleService::class), $oiConnection);
        $result     = $assistance->integrateService($this->serviceInstance()->services[0]);
        self::assertEquals($result->status(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function should_accept_activation_when_oi_server_success_and_msisdn_not_returned()
    {
        $oiActivationResponse       = (new RestResponseBuilder())->withBodyFromArray([])->withStatus(202)->success();
        $oiStatusActivationResponse = (new RestResponseBuilder()
        )->withBodyFromArray(['status' => 'FALHA', 'msisdn' => null])
            ->withStatus(400)->failure();
        $oiConnection               = \Mockery::mock(OiBRConnection::class)->makePartial();
        $oiConnection->shouldReceive('controleCartaoSale')->andReturn($oiActivationResponse);
        $oiConnection->shouldReceive('controleCartaoStatus')->andReturn($oiStatusActivationResponse);

        $assistance = new OiControleCartaoAssistance(resolve(SaleService::class), $oiConnection);
        $result     = $assistance->integrateService($this->serviceInstance()->services[0]);
        self::assertEquals($result->status(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
