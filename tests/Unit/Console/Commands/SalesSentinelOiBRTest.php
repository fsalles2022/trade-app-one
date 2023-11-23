<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use Illuminate\Support\Collection;
use OiBR\Connection\OiBRConnection;
use TradeAppOne\Console\Commands\SalesSentinelOiBR;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\TestCase;

class SalesSentinelOiBRTest extends TestCase
{
    const STATUS = 'statusTransacao';
    protected $command;

    /** @test */
    public function should_return_one_service_from_sun_when_found()
    {
        $this->app->bind(OiBRConnection::class, function () {
            $response                  = $this->getMockBuilder(RestResponse::class)
                ->setMethods(['toArray'])
                ->getMock();
            $return['statusTransacao'] = 'Concluido';
            $response->method('toArray')->will($this->returnValue($return));

            $service = $this->getMockBuilder(OiBRConnection::class)
                ->disableOriginalConstructor()
                ->setMethods(['controleBoletoQuery'])
                ->getMock();
            $service->method('controleBoletoQuery')->will($this->returnValue($response));
            return $service;
        });
        $this->command = new SalesSentinelOiBR(resolve(OiBRConnection::class), resolve(SaleService::class));
        $result        = $this->command->getActualServiceStatus('a');
        self::assertNotEmpty($result);
        self::assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_return_one_service_from_sun_when_not_found()
    {
        $this->app->bind(OiBRConnection::class, function () {
            $response = $this->getMockBuilder(RestResponse::class)
                ->setMethods(['toArray'])
                ->getMock();
            $response->method('toArray')->will($this->returnValue([]));
            $service = $this->getMockBuilder(OiBRConnection::class)
                ->disableOriginalConstructor()
                ->setMethods(['controleBoletoQuery'])
                ->getMock();
            $service->method('controleBoletoQuery')->will($this->returnValue($response));
            return $service;
        });
        $this->command = new SalesSentinelOiBR(resolve(OiBRConnection::class), resolve(SaleService::class));
        $result        = $this->command->getActualServiceStatus('oi');

        self::assertEmpty($result);
        self::assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_return_translated_status_rejected_when_sun_status()
    {
        $command = new SalesSentinelOiBR(resolve(OiBRConnection::class), resolve(SaleService::class));

        $result = $command->translateStatus([self::STATUS => 'FalhaCriacaoAssinatura']);
        self::assertEquals(ServiceStatus::REJECTED, $result['status']);
    }

    /** @test */
    public function should_return_translated_status_approved_when_sun_status()
    {
        $command = new SalesSentinelOiBR(resolve(OiBRConnection::class), resolve(SaleService::class));

        $result = $command->translateStatus([self::STATUS => 'Concluido']);
        self::assertEquals(ServiceStatus::APPROVED, $result['status']);
    }

    /** @test */
    public function should_return_translated_status_accepted_when_oi_status_pendente()
    {
        $command = new SalesSentinelOiBR(resolve(OiBRConnection::class), resolve(SaleService::class));

        $result = $command->translateStatus([self::STATUS => 'PreCadastro']);
        self::assertEquals(ServiceStatus::ACCEPTED, $result['status']);

        $result = $command->translateStatus([self::STATUS => 'PendenteRecargaAvulsa']);
        self::assertEquals(ServiceStatus::ACCEPTED, $result['status']);
    }

    /** @test */
    public function should_return_translated_status_canceled_when_sun_status()
    {
        $command = new SalesSentinelOiBR(resolve(OiBRConnection::class), resolve(SaleService::class));

        $result = $command->translateStatus([self::STATUS => 'PreCadastroExpirado']);
        self::assertEquals(ServiceStatus::CANCELED, $result['status']);

        $result = $command->translateStatus([self::STATUS => 'FalhaHabilitacaoRecargaAvulsa']);
        self::assertEquals(ServiceStatus::CANCELED, $result['status']);
    }

    protected function setUp()
    {
        parent::setUp();
    }
}
