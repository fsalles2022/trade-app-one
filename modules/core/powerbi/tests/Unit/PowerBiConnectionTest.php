<?php

namespace Core\PowerBi\tests\Unit;

use Core\PowerBi\Connections\PowerBiClient;
use Core\PowerBi\Connections\PowerBiConnection;
use Core\PowerBi\Exceptions\PowerBiExceptions;
use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class PowerBiConnectionTest extends TestCase
{
    /** @test */
    public function should_exception_when_token_invalid()
    {
        //not exists "token"
        $payload = [
            'access_token' => '123',
            'expires_in' => 123
        ];

        $this->expectExceptionCode(PowerBiExceptions::TOKEN_NOT_GENERATED);

        $response = new Response(200, [], json_encode($payload));
        $client   = \Mockery::mock(PowerBiClient::class)->makePartial();
        $client->shouldReceive('postFormParams')->twice()->andReturn(RestResponse::success($response));

        $conn = new PowerBiConnection($client);
        $conn->embedToken('23432', '3224');
    }

    /** @test */
    public function should_exception_when_login_invalid()
    {
        //not exists "access_token"
        $payload = [];

        $this->expectExceptionCode(PowerBiExceptions::TOKEN_NOT_GENERATED);

        $response = new Response(404, [], json_encode($payload));
        $client   = \Mockery::mock(PowerBiClient::class)->makePartial();
        $client->shouldReceive('postFormParams')->once()->andReturn(RestResponse::success($response));

        $conn = new PowerBiConnection($client);
        $conn->login();
    }

    /** @test */
    public function should_return_corred_embed_url()
    {
        config(['pbi.report_url' => 'tradeapp.com']);

        $conn = resolve(PowerBiConnection::class);
        $url  = $conn->embedUrl('123', '321');

        $expected = 'tradeapp.com?reportId=321&groupId=123';
        $this->assertEquals($expected, $url);
    }
}
