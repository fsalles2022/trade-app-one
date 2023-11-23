<?php

namespace TimBR\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use TimBR\Assistance\TimBRSaleAssistance;
use TimBR\Connection\Authentication\AuthenticationConnection;
use TimBR\Connection\Authentication\TimBRUserBearerHttp;
use TimBR\Connection\TimBRHttpClient;
use TimBR\Models\TimBRControleFatura;
use TimBR\Tests\Helpers\TimFactoriesHelper;
use TimBR\Tests\ServerTest\TimServerMocked;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Tests\TestCase;

class TimBRSaleAssistantTest extends TestCase
{
    use TimFactoriesHelper;

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
        Auth::setUser(new User());
        $serviceTim    = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->make(['customer' => $this->successCustomer])
            ->toArray();
        $sale          = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers()->toArray(),
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

    /** @test */
    public function should_return_message_when_is_successfull_and_area_code_11()
    {
        $this->bindAuthentication();
        Auth::setUser(new User());
        $serviceTim    = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->make(['areaCode' => 11, 'customer' => $this->successCustomer])
            ->toArray();
        $sale          = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers()->toArray(),
            'services'    => [$serviceTim]
        ]);
        $timAssistant  = resolve(TimBRSaleAssistance::class);
        $timResponse   = $timAssistant->integrateService($sale->services[0]);
        $arrayResponse = json_decode($timResponse->content(), true);
        $status        = $timResponse->status();
        self::assertEquals(Response::HTTP_OK, $status);
        self::assertEquals(trans('timBR::messages.acceptance.11'), $arrayResponse['message']);
    }

    /** @test */
    public function should_return_message_when_is_successfull_and_area_code_12()
    {
        $this->bindAuthentication();
        Auth::setUser(new User());
        $serviceTim    = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->make(['areaCode' => 12, 'customer' => $this->successCustomer])
            ->toArray();
        $sale          = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers()->toArray(),
            'services'    => [$serviceTim]
        ]);
        $timAssistant  = resolve(TimBRSaleAssistance::class);
        $timResponse   = $timAssistant->integrateService($sale->services[0]);
        $arrayResponse = json_decode($timResponse->content(), true);
        $status        = $timResponse->status();
        self::assertEquals(Response::HTTP_OK, $status);
        self::assertEquals(trans('timBR::messages.acceptance.12'), $arrayResponse['message']);
    }

    /** @test */
    public function should_return_message_when_is_successfull_and_area_code_13()
    {
        $this->bindAuthentication();
        Auth::setUser(new User());
        $serviceTim    = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->make(['areaCode' => 13, 'customer' => $this->successCustomer])
            ->toArray();
        $sale          = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers()->toArray(),
            'services'    => [$serviceTim]
        ]);
        $timAssistant  = resolve(TimBRSaleAssistance::class);
        $timResponse   = $timAssistant->integrateService($sale->services[0]);
        $arrayResponse = json_decode($timResponse->content(), true);
        $status        = $timResponse->status();
        self::assertEquals(Response::HTTP_OK, $status);
        self::assertEquals(trans('timBR::messages.acceptance.13'), $arrayResponse['message']);
    }

    /** @test */
    public function should_return_message_when_is_successfull_and_area_code_14()
    {
        $this->bindAuthentication();
        Auth::setUser(new User());
        $serviceTim    = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->make(['areaCode' => 14, 'customer' => $this->successCustomer])
            ->toArray();
        $sale          = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers()->toArray(),
            'services'    => [$serviceTim]
        ]);
        $timAssistant  = resolve(TimBRSaleAssistance::class);
        $timResponse   = $timAssistant->integrateService($sale->services[0]);
        $arrayResponse = json_decode($timResponse->content(), true);
        $status        = $timResponse->status();
        self::assertEquals(Response::HTTP_OK, $status);
        self::assertEquals(trans('timBR::messages.acceptance.14'), $arrayResponse['message']);
    }

    /** @test */
    public function should_return_message_when_is_successfull_and_area_code_15()
    {
        $this->bindAuthentication();
        Auth::setUser(new User());
        $serviceTim    = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->make(['areaCode' => 15, 'customer' => $this->successCustomer])
            ->toArray();
        $sale          = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers()->toArray(),
            'services'    => [$serviceTim]
        ]);
        $timAssistant  = resolve(TimBRSaleAssistance::class);
        $timResponse   = $timAssistant->integrateService($sale->services[0]);
        $arrayResponse = json_decode($timResponse->content(), true);
        $status        = $timResponse->status();
        self::assertEquals(Response::HTTP_OK, $status);
        self::assertEquals(trans('timBR::messages.acceptance.15'), $arrayResponse['message']);
    }

    /** @test */
    public function should_return_message_empty_when_is_successfull_and_area_code_100()
    {
        $this->bindAuthentication();
        Auth::setUser(new User());
        $serviceTim    = $this->timFactories()
            ->of(TimBRControleFatura::class)
            ->make(['areaCode' => 100, 'customer' => $this->successCustomer])
            ->toArray();
        $sale          = factory(Sale::class)->make([
            'pointOfSale' => $this->getPointOfSaleWithTimIdentifiers()->toArray(),
            'services'    => [$serviceTim]
        ]);
        $timAssistant  = resolve(TimBRSaleAssistance::class);
        $timResponse   = $timAssistant->integrateService($sale->services[0]);
        $arrayResponse = json_decode($timResponse->content(), true);
        $status        = $timResponse->status();
        self::assertEquals(Response::HTTP_OK, $status);
        self::assertArrayNotHasKey('message', $arrayResponse);
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
}
