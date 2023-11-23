<?php

namespace TradeAppOne\Tests\Unit\Domain\Middleware;

use Authorization\Tests\Helpers\Builders\ThirdPartyConfigBuilder;
use Authorization\Http\Middleware\ThirdPartiesMiddleware;
use Authorization\Services\ThirdPartyAccessConfig;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Mockery;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Facades\HeimdallInbound;
use TradeAppOne\Http\Middleware\LogAfterRequest;
use TradeAppOne\Tests\TestCase;

class LogAfterRequestMiddlewareTest extends TestCase
{
    private $normalRequest;
    private $response;
    private $thirdPartyrequest;

    public function setUp() 
    {
        parent::setUp();

        $this->normalRequest = new Request;
        $this->response = new Response();
        $this->thirdPartyrequest = $this->mockThirdPartyConf();
    }

    /** @test */
    public function should_send_to_heimdall_when_env_is_enabled_for_all()
    {
        Config::set('heimdall.inbound_request', LogAfterRequest::ALL);

        HeimdallInbound::shouldReceive('index')->once();

        (resolve(LogAfterRequest::class))->terminate(new Request, new Response());
    }

    /** @test */
    public function should_not_send_to_heimdall_when_env_is_disabled()
    {
        Config::set('heimdall.inbound_request', "invalid");

        HeimdallInbound::shouldReceive('index')->never();

        resolve(LogAfterRequest::class)->terminate($this->normalRequest, $this->response);
    }

    /** @test */
    public function should_send_to_heimdall_request_from_third_parties_when_env_is_third_parties()
    {
        Config::set('heimdall.inbound_request', LogAfterRequest::THIRD_PARTIES);

        HeimdallInbound::shouldReceive('index')
            ->withArgs([$this->thirdPartyrequest, $this->response])
            ->once();

        (resolve(LogAfterRequest::class))->terminate($this->thirdPartyrequest, $this->response);
    }

    /** @test */
    public function should_not_send_to_heimdall_a_normal_request_when_env_is_third_parties()
    {
        Config::set('heimdall.inbound_request', LogAfterRequest::THIRD_PARTIES);

        HeimdallInbound::shouldReceive('index')
            ->withArgs([$this->normalRequest, $this->response])
            ->never();

        (resolve(LogAfterRequest::class))->terminate($this->normalRequest, $this->response);
    }

    /** @test */
    public function should_exception_in_log_not_stop_process_falling_down_in_catch_block()
    {
        Config::set('heimdall.inbound_request', LogAfterRequest::ALL);

        HeimdallInbound::shouldReceive('index')->andThrow(\Exception::class);
        Log::shouldReceive('alert')->once();

        resolve(LogAfterRequest::class)->terminate($this->normalRequest, $this->response);
    }

    private function mockThirdPartyConf()
    {
        app()->bind(ThirdPartyAccessConfig::class, function (){
            $thirdPartyConfig = (new ThirdPartyConfigBuilder())
                ->withAccessKey("ACCESS_KEY")
                ->build();

            $thirdPartyAccessConfigMock = Mockery::mock(ThirdPartyAccessConfig::class);
            $thirdPartyAccessConfigMock->shouldReceive('getByClient')
                ->with(NetworkEnum::RIACHUELO)
                ->andReturn($thirdPartyConfig);

            return $thirdPartyAccessConfigMock;
        });

        $thirdPartyrequest = new Request;
        $thirdPartyrequest->headers->set(ThirdPartiesMiddleware::ACCESS_KEY, 'ACCESS_KEY');

        return $thirdPartyrequest;
    }
}