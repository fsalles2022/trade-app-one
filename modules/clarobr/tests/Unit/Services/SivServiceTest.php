<?php

namespace ClaroBR\Tests\Unit\Services;

use ClaroBR\Connection\SivConnection;
use ClaroBR\Enumerators\ClaroBRCaches;
use ClaroBR\Exceptions\ClaroExceptions;
use ClaroBR\Exceptions\PlansNotFoundException;
use ClaroBR\Services\SivService;
use ClaroBR\Tests\ClaroBRTestBook;
use ClaroBR\Tests\ServerTest\ClaroBRResponseBook;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Mockery;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\Channel;
use TradeAppOne\Domain\Models\Tables\Operator;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RestResponseBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class SivServiceTest extends TestCase
{
    use SivBindingHelper;

    private function getSivServiceInstance($parameter = null): SivService
    {
        if ($parameter !== null) {
            $repository = resolve(SaleRepository::class);
            return new SivService($parameter, $repository);
        }
        return resolve(SivService::class);
    }

    /** @test */
    public function should_return_an_instance_of_siv_service(): void
    {
        $class     = $this->getSivServiceInstance();
        $className = get_class($class);
        $this->assertEquals(SivService::class, $className);
    }

    /** @test */
    public function should_utils_for_create_sale_return(): void
    {
        $utilsResponse     = $this->getUtilsResponse();
        $sivConnectionMock = Mockery::mock(SivConnection::class)->makePartial();

        $sivConnectionMock->shouldReceive('utils')->andReturn(RestResponse::success($utilsResponse));

        $class  = $this->getSivServiceInstance($sivConnectionMock);
        $result = $class->utilsForCreateSale();

        $this->assertInternalType('array', $result);
    }

    private function getUtilsResponse(): Response
    {
        $body = stream_for(file_get_contents(ClaroBRResponseBook::SUCCESS_UTILS));
        return new Response(HttpResponse::HTTP_OK, ['Content­Type' => 'application/json'], $body);
    }

    /** @test */
    public function should_plans_with_filters_return_array(): void
    {
        $this->bindSivResponse();
        $userHelper = (new UserBuilder())->build();
        Auth::setUser($userHelper);

        $sivConnection = resolve(SivConnection::class);
        $class         = $this->getSivServiceInstance($sivConnection);
        $result        = $class->plans(['id' => '66', 'ddd' => '11']);

        $this->assertInternalType('array', $result);
    }

    /** @test */
    public function should_plans_empty_return_exception(): void
    {
        $this->bindSivResponse();
        $user      = new User();
        $user->cpf = ClaroBRTestBook::WITHOUT_PLANS_USER;
        Auth::setUser($user);

        $sivConnection = resolve(SivConnection::class);
        $class         = $this->getSivServiceInstance($sivConnection);

        $this->expectException(PlansNotFoundException::class);
        $class->plans(['id' => '66']);
    }

    /** @test */
    public function should_products_return_collection(): void
    {
        $this->bindSivResponse();
        $user      = new User();
        $user->cpf = ClaroBRTestBook::WITHOUT_PLANS_USER;
        Auth::setUser($user);

        $sivConnection = resolve(SivConnection::class);
        $class         = $this->getSivServiceInstance($sivConnection);
        $result        = $class->products(['id' => '66', 'ddd' => '11']);

        $this->assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_products_be_called_with_area_code(): void
    {
        $this->bindSivResponse();
        $user      = new User();
        $user->cpf = ClaroBRTestBook::WITHOUT_PLANS_USER;
        Auth::setUser($user);

        $response     = new Response(200, ['Content­Type' => 'application/json'], "{}");
        $restResponse = RestResponse::success($response);

        $mock = Mockery::mock(SivConnection::class);
        $mock->shouldReceive('plans')->with(['ddd' => '11'], null)->andReturn($restResponse);

        $class  = $this->getSivServiceInstance($mock);
        $result = $class->products(['areaCode' => '11']);

        $this->assertInstanceOf(Collection::class, $result);
    }

    /** @test */
    public function should_domains_return_collection(): void
    {
        $this->bindSivResponse();
        $user      = new User();
        $user->cpf = ClaroBRTestBook::WITHOUT_PLANS_USER;
        Auth::setUser($user);

        $sivConnection = resolve(SivConnection::class);
        $class         = $this->getSivServiceInstance($sivConnection);
        $result        = $class->domains();

        $this->assertInternalType('array', $result);
    }

    /** @test */
    public function should_contest_return_with_structure(): void
    {
        $this->bindSivResponse();

        $responseMocker = (new RestResponseBuilder())
            ->withBodyFromFile(ClaroBRResponseBook::CONTEST_SUCCESS)
            ->success();

        $serviceId         = '123';
        $user              = (new UserBuilder())->build();
        $sivConnectionMock = Mockery::mock(SivConnection::class);
        $sivConnectionMock
            ->shouldReceive('contest')
            ->once()
            ->andReturn($responseMocker)
            ->getMock();

        $sivService = $this->getSivServiceInstance($sivConnectionMock);
        $result     = $sivService->contest($serviceId, $user->id);

        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('plan_type', $result);
        $this->assertArrayHasKey('status', $result);
    }

    /** @test */
    public function should_contest_throw_exception_when_request_has_error(): void
    {
        $expectedMessage = 'Cannot test';
        $responseMocker  = (new RestResponseBuilder())
            ->withBodyFromArray([
                'type' => 'error',
                'message' => $expectedMessage
            ])
            ->success();

        $sivConnectionMock = Mockery::mock(SivConnection::class);
        $sivConnectionMock->shouldReceive('contest')->andReturn($responseMocker);

        $this->expectExceptionMessage($expectedMessage);

        $serviceId = '123';
        $user      = (new UserBuilder())->build();
        $this->getSivServiceInstance($sivConnectionMock)->contest($serviceId, $user->id);
    }

    /** @test */
    public function should_contest_return_exception_when_request_fail(): void
    {
        $responseMocker = (new RestResponseBuilder())
            ->withBody('{}')
            ->withStatus(500)
            ->failure();

        $sivConnectionMock = Mockery::mock(SivConnection::class);
        $sivConnectionMock->shouldReceive('contest')->andReturn($responseMocker);

        $this->expectExceptionMessage(trans('siv::exceptions.' . ClaroExceptions::CONTEST_INVALID_RESPONSE));

        $serviceId = '123';
        $user      = (new UserBuilder())->build();
        $this->getSivServiceInstance($sivConnectionMock)->contest($serviceId, $user->id);
    }

    /** @test */
    public function should_update_cache_when_utils_is_empty(): void
    {
        $this->bindSivResponse();
        $user      = new User();
        $user->cpf = ClaroBRTestBook::WITHOUT_PLANS_USER;
        Auth::setUser($user);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->withAnyArgs();
        $sivConnection = resolve(SivConnection::class);
        $class         = $this->getSivServiceInstance($sivConnection);
        $class->domains();
    }

    /** @test */
    public function should_get_cache_when_utils_exists(): void
    {
        $this->bindSivResponse();
        $user      = new User();
        $user->cpf = ClaroBRTestBook::WITHOUT_PLANS_USER;
        Auth::setUser($user);

        Cache::shouldReceive('get')->with(ClaroBRCaches::CLARO_DOMAINS)->andReturn([
            'local' => '1',
            'dueDate' => '',
            'banks' => ''
        ]);

        $sivConnection = resolve(SivConnection::class);
        $class         = $this->getSivServiceInstance($sivConnection);
        $class->domains();
    }

    /** @test */
    public function should_return_iccids_on_valid_prefix(): void
    {
        $this->bindSivResponse();
        $operator = Operator::create([
            'slug' => Operations::CLARO
        ]);
        $channel  = factory(Channel::class)->states(Channels::DISTRIBUICAO)->create();
        $role     = factory(Role::class)->create([
            'slug' => 'vendedor-promotor-inova'
        ]);
        $user     = (new UserBuilder())->withOperators($operator)->withUserChannel($channel)->withRole($role)->build();
        Auth::setUser($user);
        $connection = resolve(SivConnection::class);
        $service    = $this->getSivServiceInstance($connection);
        $iccds      = $service->availableIccids(ClaroBRTestBook::ICCID_PREFIX_WITH_SIMCARD);
        self::assertCount(10, $iccds['body']);
    }

    /** @test */
    public function should_return_valid_payload_save_authenticate(): void
    {
        $this->bindSivResponse();

        $pointOfSale = (new PointOfSaleBuilder)->withState('with_identifiers')->build();
        $user        = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($user);

        $connection = resolve(SivConnection::class);
        $sivService = $this->getSivServiceInstance($connection);

        $service = factory(Service::class)->create();
        $sale    = (new SaleBuilder())->withPointOfSale($pointOfSale)->withServices([$service])->build();
        $service = $sale->services()->first();

        $response = $sivService->saveStatus(array(
            "cpf"=> '00000009652',
            "serviceTransaction"=> data_get($service, 'serviceTransaction', ''),
        ));

        $this->assertEquals(HttpResponse::HTTP_OK, $response->getStatus());
        $this->assertEquals('Status do autentica salvo com sucesso.', $response->get('data'));
    }
}
