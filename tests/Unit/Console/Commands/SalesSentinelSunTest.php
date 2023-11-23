<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use Illuminate\Support\Collection;
use TradeAppOne\Console\Commands\SalesSentinelSun;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\TestCase;
use VivoBR\Connection\SunConnection;

class SalesSentinelSunTest extends TestCase
{
    protected $command;

    /** @test */
    public function should_return_one_service_from_sun_when_found(): void
    {
        $this->app->bind(SunConnection::class, function () {
            $response = $this->getMockBuilder(RestResponse::class)
                ->setMethods(['toArray'])
                ->getMock();
            $return['vendas'][0]['servicos'] = ['status' => 'APROVADO'];
            $response->method('toArray')->willReturn($return);

            $service = $this->getMockBuilder(SunConnection::class)
                ->disableOriginalConstructor()
                ->setMethods(['querySales', 'selectCustomConnection'])
                ->getMock();
            $service->method('selectCustomConnection')->willReturn($service);
            $service->method('querySales')->willReturn($response);
            return $service;
        });
        $this->command = new SalesSentinelSun(resolve(SunConnection::class), resolve(SaleService::class));
        $result = $this->command->getActualServiceStatus('a', ['idVenda' => 'SP-412123']);
        self::assertNotEmpty($result);
        self::assertInstanceOf(Collection::class, $result[0]);
    }

    /** @test */
    public function should_return_one_service_from_sun_when_not_found(): void
    {
        $this->app->bind(SunConnection::class, function () {
            $response = $this->getMockBuilder(RestResponse::class)
                ->setMethods(['toArray'])
                ->getMock();
            $return['vendas'] = [];
            $response->method('toArray')->willReturn($return);
            $service = $this->getMockBuilder(SunConnection::class)
                ->disableOriginalConstructor()
                ->setMethods(['querySales'])
                ->getMock();
            $service->method('querySales')->willReturn($response);
            return $service;
        });
        $this->command = new SalesSentinelSun(resolve(SunConnection::class), resolve(SaleService::class));
        $result = $this->command->getActualServiceStatus('oi', ['idVenda' => 'SP-412123']);
        self::assertEmpty($result[0]);
        self::assertEmpty($result[1]);
        self::assertInstanceOf(Collection::class, $result[0]);
    }

    /** @test */
    public function should_return_translated_status_rejected_when_sun_status()
    {
        $command = new SalesSentinelSun(resolve(SunConnection::class), resolve(SaleService::class));

        $result = $command->translateStatus(['status' => 'REPROVADO_SCORE_NEGADO_SERASA'], []);
        self::assertEquals(ServiceStatus::REJECTED, $result['status']);

        $result = $command->translateStatus(['status' => 'REPROVADO'], []);
        self::assertEquals(ServiceStatus::REJECTED, $result['status']);

        $result = $command->translateStatus(['status' => 'REPROVADO_INDISPONIBILIDADE'], []);
        self::assertEquals(ServiceStatus::REJECTED, $result['status']);

        $result = $command->translateStatus(['status' => 'REPROVADO_SCORE_NEGADO_VIVO'], []);
        self::assertEquals(ServiceStatus::REJECTED, $result['status']);
    }

    /** @test */
    public function should_return_translated_status_approved_when_sun_status(): void
    {
        $command = new SalesSentinelSun(resolve(SunConnection::class), resolve(SaleService::class));

        $result = $command->translateStatus(['status' => 'APROVADO'], []);
        self::assertEquals(ServiceStatus::APPROVED, $result['status']);
    }

    /** @test */
    public function should_return_translated_status_accepted_when_sun_status(): void
    {
        $command = new SalesSentinelSun(resolve(SunConnection::class), resolve(SaleService::class));

        $result = $command->translateStatus(['status' => 'AGUARDANDO'], []);
        self::assertEquals(ServiceStatus::ACCEPTED, $result['status']);

        $result = $command->translateStatus(['status' => 'PALITAGEM'], []);
        self::assertEquals(ServiceStatus::APPROVED, $result['status']);
    }

    /** @test */
    public function should_return_translated_status_canceled_when_sun_status(): void
    {
        $command = new SalesSentinelSun(resolve(SunConnection::class), resolve(SaleService::class));

        $result = $command->translateStatus(['status' => 'DESISTENCIA'], []);
        self::assertEquals(ServiceStatus::CANCELED, $result['status']);

        $result = $command->translateStatus(['status' => 'CANCELADO'], []);
        self::assertEquals(ServiceStatus::CANCELED, $result['status']);
    }

    /** @test */
    public function should_return_merge_service_status_and_sal_status_when_sun_status_cancelado(): void
    {
        $assert = 'CONCAT - CANCELADO';
        $command = new SalesSentinelSun(resolve(SunConnection::class), resolve(SaleService::class));

        $result = $command->translateStatus(['status' => 'CANCELADO'], ['status' => 'CONCAT']);
        self::assertEquals($assert, $result['statusThirdParty']);
        self::assertEquals(ServiceStatus::CANCELED, $result['status']);
    }

    /** @test */
    public function should_return_merge_service_status_and_sal_status_when_sun_status_desistencia(): void
    {
        $assert = 'CONCAT - DESISTENCIA';
        $command = new SalesSentinelSun(resolve(SunConnection::class), resolve(SaleService::class));

        $result = $command->translateStatus(['status' => 'DESISTENCIA'], ['status' => 'CONCAT']);
        self::assertEquals($assert, $result['statusThirdParty']);
        self::assertEquals(ServiceStatus::CANCELED, $result['status']);
    }

    /** @test */
    public function should_return_observations_for_update()
    {
        $venda = [
            'observacoes' => [
                [
                    'id' => '9',
                    'motivo' => 'Cliente não atende',
                    'origem' => 'BKO',
                    'dataHora' => '2018-05-14 11:53:54',
                    'observacao' => 'Tentar após 20:00 horas.',
                ]
            ]
        ];
        $saleToUpdate = [
            'msisdn' => '11999585806',
            'statusThirdParty' => 'FINALIZADA - REPROVADO_INDISPONIBILIDADE',
            'status' => 'REJECTED',
        ];
        $command = new SalesSentinelSun(resolve(SunConnection::class), resolve(SaleService::class));
        $saleUpdated = $command->translateObservations($saleToUpdate, $venda);
        self::assertArrayHasKey('observations', $saleUpdated);
    }

    /** @test */
    public function should_not_return_observations_for_update()
    {
        $venda = [
            'observacoes' => []
        ];
        $saleToUpdate = [
            'msisdn' => '11999585806',
            'statusThirdParty' => 'FINALIZADA - REPROVADO_INDISPONIBILIDADE',
            'status' => 'REJECTED',
        ];
        $command = new SalesSentinelSun(resolve(SunConnection::class), resolve(SaleService::class));
        $saleUpdated = $command->translateObservations($saleToUpdate, $venda);
        self::assertArrayNotHasKey('observations', $saleUpdated);
    }
}