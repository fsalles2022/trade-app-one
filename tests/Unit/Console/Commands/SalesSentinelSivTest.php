<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use ClaroBR\Connection\SivConnection;
use Illuminate\Support\Collection;
use TradeAppOne\Console\Commands\SalesSentinelSiv;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\TestCase;

class SalesSentinelSivTest extends TestCase
{
    protected $command;

    /** @test */
    public function should_return_one_service_from_siv_when_found()
    {
        $this->app->bind(SivConnection::class, function () {
            $response                                 = $this->getMockBuilder(RestResponse::class)
                ->setMethods(['toArray'])
                ->getMock();
            $return['data']['data'][0]['services'][0] = ['status' => 'AGUARDANDO'];
            $return['data']['data'][0]['id']          = '412123';
            $response->method('toArray')->will($this->returnValue($return));
            $service = $this->getMockBuilder(SivConnection::class)
                ->disableOriginalConstructor()
                ->setMethods(['querySales'])
                ->getMock();
            $service->method('querySales')->will($this->returnValue($response));
            return $service;
        });
        $this->command = new SalesSentinelSiv(resolve(SivConnection::class), resolve(SaleService::class));
        $result        = $this->command->getActualServiceStatus(['venda_id' => '412123']);
        self::assertNotEmpty($result);
        self::assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_return_one_service_from_siv_when_not_found()
    {
        $this->app->bind(SivConnection::class, function () {
            $response                                 = $this->getMockBuilder(RestResponse::class)
                ->setMethods(['toArray'])
                ->getMock();
            $return['data']['data'][0]['services'][0] = ['status' => 'AGUARDANDO'];
            $return['data']['data'][0]['id']          = '000000';
            $response->method('toArray')->will($this->returnValue($return));
            $service = $this->getMockBuilder(SivConnection::class)
                ->disableOriginalConstructor()
                ->setMethods(['querySales'])
                ->getMock();
            $service->method('querySales')->will($this->returnValue($response));
            return $service;
        });
        $this->command = new SalesSentinelSiv(resolve(SivConnection::class), resolve(SaleService::class));
        $result        = $this->command->getActualServiceStatus(['venda_id' => '412123']);
        self::assertEmpty($result);
        self::assertNotNull($result);
        self::assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_return_translated_status_empty_when_siv_status_is_not_mapped()
    {
        $command = new SalesSentinelSiv(resolve(SivConnection::class), resolve(SaleService::class));

        $result = $command->setDataToUpdate(['status' => str_random(4)], []);
        self::assertArrayNotHasKey('status', $result);
    }

    public function should_return_rejected_status_when_siv_status_is_equivalent()
    {
        $command = new SalesSentinelSiv(resolve(SivConnection::class), resolve(SaleService::class));

        $result = $command->setDataToUpdate(['status' => 'REPROVADO'], []);
        self::assertEmpty($result);

        $result = $command->setDataToUpdate(['status' => 'REPROVADA_INDISPONIBILIDADE'], []);
        self::assertEmpty($result);

        $result = $command->setDataToUpdate(['status' => 'REPROVADA_SCORE_NEGADO_VIVO'], []);
        self::assertEmpty($result);
    }

    /** @test */
    public function should_return_approved_status_when_siv_status_is_equivalent()
    {
        $command = new SalesSentinelSiv(resolve(SivConnection::class), resolve(SaleService::class));

        $result = $command->setDataToUpdate(['status' => 'APROVADO'], []);
        self::assertEquals(ServiceStatus::APPROVED, $result['status']);
    }

    /** @test */
    public function should_return_accepted_status_when_siv_status_is_equivalent()
    {
        $command = new SalesSentinelSiv(resolve(SivConnection::class), resolve(SaleService::class));

        $result = $command->setDataToUpdate(['status' => 'ENVIO_PENDENTE'], []);
        self::assertEquals(ServiceStatus::ACCEPTED, $result['status']);
        $result = $command->setDataToUpdate(['status' => 'ATIVO SEM ACEITE'], []);
        self::assertEquals(ServiceStatus::REJECTED, $result['status']);
        $result = $command->setDataToUpdate(['status' => 'ATIVADO'], []);
        self::assertEquals(ServiceStatus::ACCEPTED, $result['status']);
        $result = $command->setDataToUpdate(['status' => 'ENVIADO'], []);
        self::assertEquals(ServiceStatus::ACCEPTED, $result['status']);
        $result = $command->setDataToUpdate(['status' => 'ANALISE_RCV'], []);
        self::assertEquals(ServiceStatus::ACCEPTED, $result['status']);
    }

    /** @test */
    public function should_return_translated_status_canceled_when_siv_status()
    {
        $command = new SalesSentinelSiv(resolve(SivConnection::class), resolve(SaleService::class));

        $result = $command->setDataToUpdate(['status' => 'DESISTÊNCIA'], []);
        self::assertEquals(ServiceStatus::CANCELED, $result['status']);

        $result = $command->setDataToUpdate(['status' => 'CANCELADO'], []);
        self::assertEquals(ServiceStatus::CANCELED, $result['status']);
    }

    protected function setUp()
    {
        parent::setUp();
    }
}
