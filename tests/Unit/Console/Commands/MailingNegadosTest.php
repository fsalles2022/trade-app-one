<?php


namespace TradeAppOne\Tests\Unit\Console\Commands;

use ClaroBR\Connection\SivConnection;
use ClaroBR\Connection\VertexConnection;
use Mockery;
use TradeAppOne\Console\Commands\MailingNegados;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class MailingNegadosTest extends TestCase
{
    private const VERTEX_OK = 0;
    private const VERTEX_SOURCE_ERROR = 1;
    private const VERTEX_DATA_ERROR = 2;
    protected $command;

    /** @test */
    public function should_return_venda_negados(): void
    {
        $this->app->bind(SivConnection::class, function () {
            $response = $this->getMockBuilder(RestResponse::class)
                ->setMethods(['toArray'])
                ->getMock();
            $return = [
                'data' => [
                    'id' => 26019,
                    'plano_tipo' => 'CLARO_PRE_PAGO',
                    'numero_acesso' => '+5511994571312',
                ],
            ];
            $response->method('toArray')->willReturn($return);
            $service = $this->getMockBuilder(SivConnection::class)
                ->disableOriginalConstructor()
                ->setMethods(['getNegados'])
                ->getMock();
            $service->method('getNegados')->willReturn($response);
            return $service;
        });
        $this->command = new MailingNegados(resolve(SivConnection::class), resolve(VertexConnection::class));
        $result = $this->command->getNegados();
        self::assertNotEmpty($result);
    }

    /** @test */
    public function should_return_response_when_data_sent_to_vertex(): void
    {
        $this->app->bind(VertexConnection::class, static function () {
            return Mockery::mock(VertexConnection::class)
                ->shouldReceive('sendData')
                ->with(Mockery::subset([
                    'source' => 'tradeupvarejonegados',
                    'data' => [],
                ]))
                ->andReturn([
                    'status' => 0,
                    'message' => 'Lead recebidos com sucesso',
                ])->getMock();
        });
        $connection = resolve(VertexConnection::class);
        $response = $connection->sendData(['source' => 'tradeupvarejonegados', 'data' => []]);
        self::assertEquals(self::VERTEX_OK, $response['status']);
    }

    /** @test */
    public function should_return_error_when_source_not_sent_to_vertex(): void
    {
        $this->app->bind(VertexConnection::class, static function () {
            return Mockery::mock(VertexConnection::class)
                ->shouldReceive('sendData')
                ->with(Mockery::subset([
                    'source' => null,
                    'data' => [],
                ]))
                ->andReturn([
                    'status' => 1,
                    'message' => 'source inválido',
                ])->getMock();
        });
        $connection = resolve(VertexConnection::class);
        $response = $connection->sendData(['source' => null, 'data' => []]);
        self::assertEquals(self::VERTEX_SOURCE_ERROR, $response['status']);
    }

    /** @test */
    public function should_return_error_when_data_not_sent_to_vertex(): void
    {
        $this->app->bind(VertexConnection::class, static function () {
            return Mockery::mock(VertexConnection::class)
                ->shouldReceive('sendData')
                ->with(Mockery::subset([
                    'source' => 'tradeupvarejonegados',
                    'data' => null,
                ]))
                ->andReturn([
                    'status' => 2,
                    'message' => 'data inválido',
                ])->getMock();
        });
        $connection = resolve(VertexConnection::class);
        $response = $connection->sendData(['source' => 'tradeupvarejonegados', 'data' => null]);
        self::assertEquals(self::VERTEX_DATA_ERROR, $response['status']);
    }
}