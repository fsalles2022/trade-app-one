<?php

namespace TimBR\Tests\Unit\Services\TimBROperationsAssistancesTest;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use TimBR\Assistance\TimBROperationsAssistances\TimBRControleExpressAssistance;
use TimBR\Assistance\TimBRSaleAssistance;
use TimBR\Connection\Authentication\AuthenticationConnection;
use TimBR\Connection\Authentication\TimBRUserBearerHttp;
use TimBR\Connection\TimBRConnection;
use TimBR\Connection\TimBRHttpClient;
use TimBR\Connection\TimExpress\TimBRExpressConnection;
use TimBR\Models\TimBRExpress;
use TimBR\Services\TimBRService;
use TimBR\Tests\Helpers\TimBRBindServers;
use TimBR\Tests\Helpers\TimFactoriesHelper;
use TimBR\Tests\ServerTest\TimServerMocked;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Tests\TestCase;

class TimBRControleExpressAssistanceTest extends TestCase
{
    use TimFactoriesHelper, TimBRBindServers;

    public $successCustomer = [
        'email'            => 'as@h.com',
        'firstName'        => 'teste',
        'lastName'         => 'teste',
        'cpf'              => '00000009652',
        'gender'           => 'M',
        'birthday'         => '09/09/2018',
        'filiation'        => 'teste',
        'mainPhone'        => 'teste',
        'secondaryPhone'   => 'teste',
        'salaryRange'      => 'teste',
        'profession'       => 'teste',
        'maritalStatus'    => 'teste',
        'rg'               => 'teste',
        'rgLocal'          => 'teste',
        'rgDate'           => 'teste',
        'rgState'          => 'teste',
        'number'           => 'teste',
        'zipCode'          => 'teste',
        'neighborhood'     => 'teste',
        'neighborhoodType' => 'teste',
        'local'            => 'teste',
        'localId'          => 'teste',
        'city'             => 'teste',
        'state'            => 'SP',
        'country'          => 'Brasil'
    ];

    /** @test */
    public function should_return_message_when_is_successfull()
    {
        $this->bindAuthentication();
        $this->bindTimResponse();
        $this->mockBearerReturnFromCache();
        $network                          = factory(Network::class)->make(['slug' => 'rede'])->toArray();
        $pointOfSale                      = factory(PointOfSale::class)->make(['network' => $network]);
        $pointOfSale->providerIdentifiers = json_encode(["TIM" => 'ada']);
        Auth::setUser(new User());
        $serviceTim    = $this->timFactories()
            ->of(TimBRExpress::class)
            ->make(['customer' => $this->successCustomer])
            ->toArray();
        $sale          = factory(Sale::class)->make([
            'pointOfSale' => $pointOfSale->toArray(),
            'services'    => [$serviceTim]
        ]);
        $timAssistant  = resolve(TimBRSaleAssistance::class);
        $timResponse   = $timAssistant->integrateService($sale->services[0]);
        $arrayResponse = json_decode($timResponse->content(), true);
        self::assertArrayHasKey('message', $arrayResponse);
    }

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
                ->setConstructorArgs([
                    $this->app->make(TimBRUserBearerHttp::class),
                    $this->app->make(NetworkService::class)
                ])
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

    public function mockBearerReturnFromCache()
    {
        Cache::shouldReceive('get')
            ->withAnyArgs()
            ->andReturn('eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCIsIng1dCI6Im
            FXOExYcXE5a1l5NHBaNVQtOHRFb19IaVlTSSIsImtpZCI6Im9yYWtleSJ9.eyJzdWIi
            OiIyMTkzNTAwNzg5MCIsIm9yYWNsZS5vYXV0aC51c2VyX29yaWdpbl9pZF90eXBlIjo
            iTERBUF9VSUQiLCJvcmFjbGUub2F1dGgudXNlcl9vcmlnaW5faWQiOiIyMTkzNTAwNz
            g5MCIsImlzcyI6Ind3dy5vcmFjbGUuZXhhbXBsZS5jb20iLCJsYXN0bmFtZSI6IlQzND
            k5NTMwIiwib3JhY2xlLm9hdXRoLnN2Y19wX24iOiJPQXV0aFNlcnZpY2VQcm9maWxlI
            iwiaWF0IjoxNTM3OTgyNTc4LCJvcmFjbGUub2F1dGgucHJuLmlkX3R5cGUiOiJMREFQ
            X1VJRCIsIm9yYWNsZS5vYXV0aC50a19jb250ZXh0IjoicmVzb3VyY2VfYWNjZXNzX3R
            rIiwiZXhwIjoxNTM4MDY4OTc4LCJwcm4iOiIyMTkzNTAwNzg5MCIsImp0aSI6ImJiZTB
            mMDg1LTcwNTUtNGI2ZC05YmJlLWE5YWQ3YTllMDRjZCIsIm9yYWNsZS5vYXV0aC5zY29
            wZSI6IlRJTVZhcmVqb1JlZGUudmVuZGEiLCJjb21tb25uYW1lIjoiMjE5MzUwMDc4OTA
            iLCJvcmFjbGUub2F1dGguY2xpZW50X29yaWdpbl9pZCI6InBlcm5hbWJ1Y2FuYXMiLCJ
            vcmFjbGUub2F1dGguaWRfZF9pZCI6IjEyMzQ1Njc4LTEyMzQtMTIzNC0xMjM0LTEyMzQ
            1Njc4OTAxMiIsInVzZXIudGVuYW50Lm5hbWUiOiJEZWZhdWx0RG9tYWluIn0.O0dHehQ
            1fjjEfljWK-mUbtveW4XF2HIVkdpJ-mLexoCuErrpBmi8mq7bv1HHvyzB9cpY1WqvMl4
            3zPeZ3bKodgiL6lNhNUlogdl394W3pOosN8V167gR5rrN-irv4a_M72wLpRhc_-6PNCh
            -73GCGDksFUeH5R1KtAqR9esVIts');
    }

    /** @test */
    public function should_return_order_when_integration_is_success()
    {
        $this->bindAuthentication();
        $this->bindTimResponse();
        $this->mockBearerReturnFromCache();
        $network                          = factory(Network::class)->make(['slug' => 'rede'])->toArray();
        $pointOfSale                      = factory(PointOfSale::class)->make(['network' => $network]);
        $pointOfSale->providerIdentifiers = json_encode(["TIM" => 'ada']);
        Auth::setUser(new User());
        $serviceTim    = $this->timFactories()
            ->of(TimBRExpress::class)
            ->make(['areaCode' => 15, 'customer' => $this->successCustomer, 'eligibilityToken' => '20188888'])
            ->toArray();
        $sale          = factory(Sale::class)->make([
            'pointOfSale' => $pointOfSale->toArray(),
            'services'    => [$serviceTim]
        ]);
        $timAssistant  = resolve(TimBRSaleAssistance::class);
        $timResponse   = $timAssistant->integrateService($sale->services[0]);
        $arrayResponse = json_decode($timResponse->content(), true);
        $status        = $timResponse->status();

        self::assertEquals(Response::HTTP_OK, $status);
        self::assertArrayHasKey('order', $arrayResponse);
    }

    public function bindIncompleteRepositories(Sale $saleEntity)
    {
        $this->app->bind(SaleRepository::class, function () use ($saleEntity) {
            $repository = $this->getMockBuilder(SaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['findInSale', 'find', 'updateService'])
                ->getMock();

            $service                      = $saleEntity->services()->first();
            $service->operatorIdentifiers = [];

            $repository->method('find')->will($this->returnValue($saleEntity));
            $repository->method('findInSale')->will($this->returnValue($service));
            $repository->method('updateService')->will($this->returnValue($service));
            return $repository;
        });
    }

    public function bindRepositories(Sale $saleEntity)
    {
        $this->app->bind(SaleRepository::class, function () use ($saleEntity) {
            $repository = $this->getMockBuilder(SaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['findInSale', 'find', 'updateService', 'pushLogService'])
                ->getMock();

            $service = $saleEntity->services()->first();

            $repository->method('find')->will($this->returnValue($saleEntity));
            $repository->method('findInSale')->will($this->returnValue($service));
            $repository->method('updateService')->will($this->returnValue($service));
            $repository->method('pushLogService')->will($this->returnValue($service));
            return $repository;
        });
    }

    /** @test */
    public function should_call_cancel_subscription_when_subscription_exists_status_submited()
    {
        $repository      = \Mockery::mock(SaleRepository::class)->makePartial();
        $expressResponse = \Mockery::mock(RestResponse::class)->makePartial();
        $timResponse     = \Mockery::mock(RestResponse::class)->makePartial();
        $express         = \Mockery::mock(TimBRExpressConnection::class)->makePartial();
        $tim             = \Mockery::mock(TimBRService::class)->makePartial();

        $service     = $this->timFactories()->of(TimBRExpress::class)->make(['status' => ServiceStatus::SUBMITTED])
            ->toArray();
        $network     = factory(Network::class)->create(['slug' => 'cea']);
        $pointOfSale = factory(PointOfSale::class)->create([
            'networkId'              => $network->id,
            'providerIdentifiers' =>
                json_encode([Operations::TIM => '123'])
        ]);
        $pointOfSale->network;
        $this->mockBearerReturnFromCache();
        $sale = factory(Sale::class)->create([
            'pointOfSale' => $pointOfSale->toArray(),
            'services'    => [$service]
        ]);

        $repository->shouldReceive('pushLogService')->once();
        $repository->shouldReceive('updateService')->once();

        $timResponse->shouldReceive('toArray')->andReturn(['order' => ['protocol', 'contract']]);
        $timResponse->shouldReceive('getStatus')->andReturn(Response::HTTP_OK);

        $expressResponse->shouldReceive('toArray')->andReturn(['responseDescription' => 'Já existe uma ordem com esse número']);
        $expressResponse->shouldReceive('getStatus')->andReturn(Response::HTTP_OK);

        $express->shouldReceive('customerSubscription')->never();

        $tim->shouldReceive('generateOrder')->once()->andReturn($timResponse);

        $assistance = new TimBRControleExpressAssistance($repository, $tim, $express);
        $result     = $assistance->activate($sale->services->first());
        self::assertArrayHasKey('message', $result->getAdapted());
    }
}
