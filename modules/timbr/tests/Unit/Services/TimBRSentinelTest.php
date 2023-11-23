<?php

namespace TimBR\Tests\Unit\Services;

use TimBR\Connection\TimBRConnection;
use TimBR\Enumerators\TimBRStatus;
use TimBR\Models\TimBRControleFatura;
use TimBR\Services\TimBRSentinel;
use TimBR\Tests\Helpers\TimFactoriesHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\TestCase;

class TimBRSentinelTest extends TestCase
{
    use TimFactoriesHelper;

    /** @test */
    public function should_update_tim_service_with_approved_and_less_than_five_tries_when_return_success_payload_from_integration()
    {
        $this->bindAuthentication([
            'protocol' => '2019061728403',
            'status' => data_get(TimBRStatus::APPROVED, 0),
        ]);

        $saleOptions = [
            'saleTransaction' => '202111151804189699',
            'services' => [
                ['timProtocolSearchTries' => 2]
            ]
        ];
        $this->mockSale($saleOptions);

        $sentinel                = new TimBRSentinel(resolve(TimBRConnection::class), resolve(SaleService::class));
        $result                  = $sentinel->sentinelDailySalesByProtocol();
        $firstServiceTransaction = $saleOptions['saleTransaction'] . '-0';

        self::assertEquals(ServiceStatus::APPROVED, data_get($result, '0.status'));
        self::assertEquals(
            ++$saleOptions['services'][0]['timProtocolSearchTries'],
            data_get($result, $firstServiceTransaction.'.timProtocolSearchTries')
        );
    }

    /** @test */
    public function should_not_update_tim_service_without_approved_and_less_than_five_tries_when_return_incorrect_payload_from_integration()
    {
        $this->bindAuthentication([]);

        $saleOptions = [
            'saleTransaction' => '202111151804189699',
            'services' => [
                ['timProtocolSearchTries' => 4]
            ]
        ];
        $this->mockSale($saleOptions);

        $sentinel                = new TimBRSentinel(resolve(TimBRConnection::class), resolve(SaleService::class));
        $result                  = $sentinel->sentinelDailySalesByProtocol();
        $firstServiceTransaction = $saleOptions['saleTransaction'] . '-0';

        self::assertEquals(
            ++$saleOptions['services'][0]['timProtocolSearchTries'],
            data_get($result, $firstServiceTransaction.'.timProtocolSearchTries')
        );
    }

    /** @test */
    public function should_not_update_tim_service_more_than_five_tries_in_daily_method()
    {
        $this->bindAuthentication([]);

        $saleOptions = [
            'saleTransaction' => '202111151804189699',
            'services' => [
                ['timProtocolSearchTries' => 6]
            ]
        ];
        $this->mockSale($saleOptions);

        $sentinel = new TimBRSentinel(resolve(TimBRConnection::class), resolve(SaleService::class));
        $result   = $sentinel->sentinelDailySalesByProtocol();
        self::assertEmpty($result);
    }

    /** @test */
    public function should_update_tim_service_with_approved_and_more_than_eight_tries_in_yearly_method_when_return_success_payload_from_integration()
    {
        $this->bindAuthentication([
            'protocol' => '2019061728403',
            'status' => data_get(TimBRStatus::APPROVED, 0),
        ]);

        $saleOptions = [
            'saleTransaction' => '202111151804189999',
            'services' => [
                ['timProtocolSearchTries' => 8]
            ]
        ];
        $this->mockSale($saleOptions);

        $sentinel                = new TimBRSentinel(resolve(TimBRConnection::class), resolve(SaleService::class));
        $result                  = $sentinel->sentinelYearlySalesByProtocol();
        $firstServiceTransaction = $saleOptions['saleTransaction'] . '-0';

        self::assertEquals(ServiceStatus::APPROVED, data_get($result, '0.0.status'));
        self::assertEquals(
            ++$saleOptions['services'][0]['timProtocolSearchTries'],
            data_get($result, '0.'.$firstServiceTransaction.'.timProtocolSearchTries')
        );
    }

    protected function bindAuthentication(array $arrayResponse = []): void
    {
        $connection = \Mockery::mock(TimBRConnection::class)->makePartial();
        $response   = \Mockery::mock(Responseable::class)->makePartial();

        $response->shouldReceive('toArray')->andReturn($arrayResponse);
        $connection->shouldReceive('getOrderStatusByProtocol')->withAnyArgs()->andReturn($response);
        $connection->shouldReceive('selectCustomConnection')->andReturnSelf();

        app()->bind(TimBRConnection::class, function () use ($connection) {
            return $connection;
        });
    }

    protected function mockSale(array $saleOptions = []): void
    {
        $defaultTransaction = $saleOptions['saleTransaction'] ?? '202103151804189631';

        $controleFatura = $this->timFactories()->of(TimBRControleFatura::class)->create([
            'status' => ServiceStatus::ACCEPTED,
            'serviceTransaction' => $defaultTransaction.'-0',
            'timProtocolSearchTries' => $saleOptions['services'][0]['timProtocolSearchTries'] ?? 1
        ]);
        $network        = factory(Network::class)->make(['slug' => 'cea']);
        $pointOfSale    = factory(PointOfSale::class)->make([
            'network'              => $network,
            'providerIdentifiers' =>
                json_encode([Operations::TIM => '123'])
        ]);
        $pointOfSale->setRelation('network', $network);

        factory(Sale::class)->create([
            'pointOfSale' => $pointOfSale->toArray(),
            'services'    => [$controleFatura->toArray()],
            'saleTransaction' => $defaultTransaction
        ]);
    }
}
