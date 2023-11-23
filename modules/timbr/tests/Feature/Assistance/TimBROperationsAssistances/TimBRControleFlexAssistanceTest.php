<?php

declare(strict_types=1);

namespace TimBR\Tests\Feature\Assistance\TimBROperationsAssistances;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Symfony\Component\HttpFoundation\Response;
use TimBR\Assistance\TimBROperationsAssistances\TimBRControleFlexAssistance;
use TimBR\Connection\Authentication\AuthenticationConnection;
use TimBR\Connection\Authentication\TimBRUserBearerHttp;
use TimBR\Connection\TimBRHttpClient;
use TimBR\Exceptions\TimBROrder;
use TimBR\Models\TimBRControleFlex;
use TimBR\Tests\Helpers\TimFactoriesHelper;
use TimBR\Tests\ServerTest\TimServerMocked;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceAlreadyInProgress;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class TimBRControleFlexAssistanceTest extends TestCase
{
    use TimFactoriesHelper;

    public function bindAuthentication()
    {
        $mock    = new TimServerMocked();
        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);
        $client  = new TimBRHttpClient($client);

        $this->app->bind(TimBRUserBearerHttp::class, function () {
            $mock = $this->getMockBuilder(TimBRUserBearerHttp::class)
                ->disableOriginalConstructor()
                ->setMethods(['requestBearer'])
                ->getMock();

            $mock->method('requestBearer')->willReturn([
                'bearertokenabcdefgh12345667',
                1234567898,
            ]);

            return $mock;
        });

        $this->app->bind(AuthenticationConnection::class, function () use ($client) {
            $mock = $this->getMockBuilder(AuthenticationConnection::class)
                ->setConstructorArgs([$this->app->make(TimBRUserBearerHttp::class), $this->app->make(NetworkService::class)])
                ->setMethods(['authenticateNetwork', 'authUser', 'getClient', 'getClientForOrder', 'getPMIDClient'])
                ->getMock();
            $mock->method('authUser')->withAnyParameters()->willReturn($client);
            $mock->method('getClient')->withAnyParameters()->willReturn($client);
            $mock->method('getClientForOrder')->withAnyParameters()->willReturn($client);
            $mock->method('getPMIDClient')->withAnyParameters()->willReturn($client);
            $mock->method('authenticateNetwork')->willReturn($client);
            return $mock;
        });
    }

    public function test_should_throw_exception_when_status_rejected(): void
    {
        $this->bindAuthentication();

        $controleFlexAssistance = $this->getInstanceAssistance();

        $serviceControleFlex = $this->timFactories()
            ->of(TimBRControleFlex::class)
            ->make()
            ->toArray();

        $serviceControleFlex['status'] = ServiceStatus::REJECTED;

        /** @var Sale $sale */
        $sale = factory(Sale::class)
            ->create([
                'services' => [$serviceControleFlex],
                'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers()->toArray()
            ]);

        $this->expectException(ServiceAlreadyInProgress::class);
        $controleFlexAssistance->activate($sale->services->first());
    }

    public function test_should_send_sale_successfully(): void
    {
        $this->bindAuthentication();

        $controleFlexAssistance = $this->getInstanceAssistance();

        $serviceControleFlex = $this->timFactories()
            ->of(TimBRControleFlex::class)
            ->states(['userSuccess'])
            ->make();

        $network = NetworkBuilder::make()->build();

        $pointOfSale                      = PointOfSaleBuilder::make()->withNetwork($network)->build();
        $pointOfSale->providerIdentifiers = json_encode(['TIM' => 'ada']);
        $pointOfSale->save();

        $sale = SaleBuilder::make()
            ->withServices([$serviceControleFlex])
            ->withPointOfSale($pointOfSale)
            ->build();

        $response = $controleFlexAssistance->activate($sale->services->first());
        $this->assertInstanceOf(ResponseAdapterAbstract::class, $response);
        $this->arrayHasKey('order');
        $this->arrayHasKey('message');
        $this->assertSame($response->getStatus(), Response::HTTP_OK);
        $this->assertDatabaseHas('sales', ['saleTransaction' => $sale->saleTransaction], 'mongodb');
    }

    public function test_should_send_failed_sale(): void
    {
        $this->bindAuthentication();

        $controleFlexAssistance = $this->getInstanceAssistance();

        $serviceControleFlex = $this->timFactories()
            ->of(TimBRControleFlex::class)
            ->make();

        $network = NetworkBuilder::make()->withSlug('rede')->build();

        $pointOfSale                      = PointOfSaleBuilder::make()->withNetwork($network)->build();
        $pointOfSale->providerIdentifiers = json_encode(['TIM' => 'ada']);
        $pointOfSale->save();

        $sale = SaleBuilder::make()
            ->withServices([$serviceControleFlex])
            ->withPointOfSale($pointOfSale)
            ->build();

        $this->expectException(TimBROrder::class);
        $this->expectExceptionMessage('Nao foi possivel realizar a ativacao do numero neste chip. Tente novamente com um novo chip');

        $controleFlexAssistance->activate($sale->services->first());
    }

    private function getInstanceAssistance(): TimBRControleFlexAssistance
    {
        return resolve(TimBRControleFlexAssistance::class);
    }
}
