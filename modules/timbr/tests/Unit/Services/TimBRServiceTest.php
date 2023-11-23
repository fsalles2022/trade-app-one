<?php

namespace TimBR\Tests\Unit\Services;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Event;
use TimBR\Connection\Authentication\AuthenticationConnection;
use TimBR\Connection\Authentication\TimBRUserBearerHttp;
use TimBR\Connection\TimBRHttpClient;
use TimBR\Exceptions\TimBRAreaCodeZipCode;
use TimBR\Exceptions\TimBREligibilityException;
use TimBR\Services\TimBRService;
use TimBR\Tests\Helpers\TimBRBindServers;
use TimBR\Tests\Helpers\TimBRSaleHelper;
use TimBR\Tests\ServerTest\TimServerMocked;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Events\PreAnalysisEvent;
use TradeAppOne\Tests\TestCase;

class TimBRServiceTest extends TestCase
{
    use TimBRSaleHelper, TimBRBindServers;

    /** @test */
    public function should_return_exception_when_area_code_no_belongs_to_state()
    {
        $this->expectException(TimBRAreaCodeZipCode::class);
        $this->expectExceptionCode('TimBRAreaCodeZipCodeValidation');
        $payload = [
            'areaCode' => '67',
            'customer' => [
                'zipCode' => '06454000',
            ]
        ];
        $this->bindAuthentication();
        $utils   = $this->bindServices();
        $service = resolve(TimBRService::class);

        $service->eligibility($utils['user'], $utils['pointOfSale']['id'], $payload);
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

    public function bindServices()
    {
        $utils         = $this->utilsForUnitTests();
        $utils['user'] = $this->mockUserHierarchyRelation($utils);
        $this->app->bind(PointOfSaleRepository::class, function () use ($utils) {
            $repository = $this->getMockBuilder(PointOfSaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['find'])
                ->getMock();

            $repository->method('find')->will($this->returnValue($utils['pointOfSale']));
            return $repository;
        });

        $this->app->bind(PointOfSaleService::class, function () use ($utils) {
            $service = $this->getMockBuilder(PointOfSaleService::class)
                ->setConstructorArgs([$this->app->make(PointOfSaleRepository::class)])
                ->setMethods(['authenticatedUserPointsOfSale'])
                ->getMock();

            $service
                ->method('authenticatedUserPointsOfSale')
                ->will($this->returnValue(collect([$utils['pointOfSale']])));

            return $service;
        });

        return $utils;
    }

    public function mockUserHierarchyRelation($utils)
    {
        $hierarchy = $this->getMockBuilder(Hierarchy::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPointsOfSaleAttribute', 'getParentAttribute'])
            ->getMock();
        $hierarchy->method('getPointsOfSaleAttribute')
            ->willReturn(collect([$utils['pointOfSale']]));
        $hierarchy->method('getParentAttribute')
            ->willReturn($this->returnValue('1'));
        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPointsOfSaleAttribute', 'getHierarchiesAttribute', 'getCpfAttribute', 'getNetwork'])
            ->getMock();
        $user->method('getPointsOfSaleAttribute')->willReturn(collect([$utils['pointOfSale']]));
        $user->method('getHierarchiesAttribute')->willReturn(collect([$hierarchy]));
        $user->method('getCpfAttribute')->willReturn('12312');
        $user->method('getNetwork')->willReturn(new Network(['slug' => 'abc']));
        return $user;
    }

    /** @test */
    public function should_return_eligibility()
    {
        $payload = [
            'areaCode'  => '11',
            'operation' => Operations::TIM_CONTROLE_FATURA,
            'customer'  => [
                'cpf'       => '06454000',
                'firstName' => '06454000',
                'lastName'  => '06454000',
                'filiation' => '06454000',
                'zipCode'   => '06454000',
                'birthday'  => '2018-10-12'
            ]
        ];
        $this->bindAuthentication();
        $utils   = $this->bindServices();
        $service = resolve(TimBRService::class);

        $result = $service->eligibility($utils['user'], $utils['pointOfSale']['id'], $payload);
        $result = $result->getAdapted();
        self::assertArrayHasKey('product', $result[0]);
    }

    /** @test */
    public function should_pre_analysis_event_be_called_in_tim_elegibility()
    {
        Event::fake();
        $payload = [
            'areaCode'  => '11',
            'operation' => Operations::TIM_CONTROLE_FATURA,
            'customer'  => [
                'cpf'       => '06454000',
                'firstName' => '06454000',
                'lastName'  => '06454000',
                'filiation' => '06454000',
                'zipCode'   => '06454000',
                'birthday'  => '2018-10-12'
            ]
        ];
        $this->bindAuthentication();
        $utils   = $this->bindServices();
        $service = resolve(TimBRService::class);

        $service->eligibility($utils['user'], $utils['pointOfSale']['id'], $payload);
        Event::assertDispatched(PreAnalysisEvent::class);
    }

    /** @test */
    public function should_return_eligibility_with_msisdn()
    {
        $payload = [
            'msisdn'    => '11991938845',
            'operation' => Operations::TIM_CONTROLE_FATURA,
            'customer'  => [
                'cpf'       => '06454000',
                'firstName' => '06454000',
                'lastName'  => '06454000',
                'filiation' => '06454000',
                'zipCode'   => '06454000',
                'birthday'  => '2018-10-12'
            ]
        ];
        $this->bindAuthentication();
        $utils   = $this->bindServices();
        $service = resolve(TimBRService::class);

        $result = $service->eligibility($utils['user'], $utils['pointOfSale']['id'], $payload);
        $result = $result->getAdapted();
        self::assertArrayHasKey('product', $result[0]);
    }

    /** @test */
    public function should_return_eligibility_with_ported_number()
    {
        $payload = [
            'portedNumber' => '11991938845',
            'operation'    => Operations::TIM_CONTROLE_FATURA,
            'customer'     => [
                'cpf'       => '06454000',
                'firstName' => '06454000',
                'lastName'  => '06454000',
                'filiation' => '06454000',
                'zipCode'   => '06454000',
                'birthday'  => '2018-10-12'
            ]
        ];
        $this->bindAuthentication();
        $utils   = $this->bindServices();
        $service = resolve(TimBRService::class);

        $result = $service->eligibility($utils['user'], $utils['pointOfSale']['id'], $payload);
        $result = $result->getAdapted();
        self::assertArrayHasKey('product', $result[0]);
    }

    /** @test */
    public function should_return_excpetio_when_cant_get_area_code()
    {
        $this->expectException(TimBRAreaCodeZipCode::class);

        $payload = [
            'operation' => Operations::TIM_CONTROLE_FATURA,
            'customer'  => [
                'cpf'       => '06454000',
                'firstName' => '06454000',
                'lastName'  => '06454000',
                'filiation' => '06454000',
                'zipCode'   => '06454000',
                'birthday'  => '2018-10-12'
            ]
        ];
        $this->bindAuthentication();
        $utils   = $this->bindServices();
        $service = resolve(TimBRService::class);

        $result = $service->eligibility($utils['user'], $utils['pointOfSale']['id'], $payload);
        $result = $result->getAdapted();
        self::assertArrayHasKey('product', $result[0]);
    }


    /** @test */
    public function should_return_eligibility_when_all_attributes_sent_area_code_priority()
    {
        $payload = [
            'operation'    => Operations::TIM_CONTROLE_FATURA,
            'areaCode'     => '11',
            'portedNumber' => '67991938845',
            'msisdn'       => '32991938845',
            'customer'     => [
                'cpf'       => '06454000',
                'firstName' => '06454000',
                'lastName'  => '06454000',
                'filiation' => '06454000',
                'zipCode'   => '06454000',
                'birthday'  => '2018-10-12'
            ]
        ];
        $this->bindAuthentication();
        $utils   = $this->bindServices();
        $service = resolve(TimBRService::class);

        $result = $service->eligibility($utils['user'], $utils['pointOfSale']['id'], $payload);
        $result = $result->getAdapted();
        self::assertArrayHasKey('product', $result[0]);
    }

    /** @test */
    public function should_return_eligibility_when_all_attributes_sent_msisnd_priority()
    {
        $payload = [
            'operation'    => Operations::TIM_CONTROLE_FATURA,
            'portedNumber' => '83991938845',
            'msisdn'       => '11991938845',
            'customer'     => [
                'cpf'       => '06454000',
                'firstName' => '06454000',
                'lastName'  => '06454000',
                'filiation' => '06454000',
                'zipCode'   => '06454000',
                'birthday'  => '2018-10-12'
            ]
        ];
        $this->bindAuthentication();
        $utils   = $this->bindServices();
        $service = resolve(TimBRService::class);

        $result = $service->eligibility($utils['user'], $utils['pointOfSale']['id'], $payload);
        $result = $result->getAdapted();
        self::assertArrayHasKey('product', $result[0]);
    }

    /** @test */
    public function should_return_eligibility_when_all_attributes_dont_sent()
    {
        $this->expectException(TimBREligibilityException::class);
        $payload = [
            'portedNumber' => '83991938845',
            'msisdn'       => '11991938845',
            'customer'     => [
                'cpf'       => '06454000',
                'firstName' => '06454000',
                'lastName'  => '06454000',
                'filiation' => '06454000',
                'zipCode'   => '06454000',
                'birthday'  => '2018-10-12'
            ]
        ];
        $this->bindAuthentication();
        $utils   = $this->bindServices();
        $service = resolve(TimBRService::class);

        $result = $service->eligibility($utils['user'], $utils['pointOfSale']['id'], $payload);
        self::assertArrayHasKey('product', $result[0]);
    }
}
