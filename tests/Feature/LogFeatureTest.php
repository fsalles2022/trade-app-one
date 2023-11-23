<?php

namespace TradeAppOne\Tests\Feature;

use Elasticsearch\ClientBuilder;
use Illuminate\Http\Response;
use Mockery;
use TradeAppOne\Tests\Helpers\UserHelper;
use TradeAppOne\Tests\TestCase;

class LogFeatureTest extends TestCase
{
    use UserHelper;

    const ENDPOINT = '/log';

    /** @test */
    public function post_should_persist_log_in_heimdall()
    {
        $this->mockClientBuilder();

        $user  = $this->userWithPermissions()['user'];
        $token = $this->loginUser($user)['token'];

        $response = $this
            ->withHeader('Authorization', $token)
            ->post(self::ENDPOINT, $this->getRequestPayload());

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertExactJson(['message' => 'Log Saved']);
    }

    /** @test */
    public function post_should_persist_log_with_incorrect_log()
    {
        $this->mockClientBuilder();
        $user  = $this->userWithPermissions()['user'];
        $token = $this->loginUser($user)['token'];

        $response = $this
            ->withHeader('Authorization', $token)
            ->post(self::ENDPOINT, ['error' => 'INVALID']);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertExactJson(['message' => 'Log Saved']);
    }

    private function getRequestPayload()
    {
        return array (
            'name' => 'M4U_MODAL_LOADED',
            'realm' => 'CLARO',
            'executionTime' => 4306,
            'urlM4u' => 'https://planosclarocontrole.m4u.com.br/tradeup/?sid=Wq2lsY1xIIbFVs9VF3aQb02qNVDLCCsnwHWhR%2FNe%2BWkeJfKx3bs9iA%3D%3D&idp=rGsKspmPIBI%3D&pw=jRBoYkB9sIm%2FbS%2FHdY4Q%2FA%3D%3D&ms=0yLJIcbtnOkAflD7Kq9C2A%3D%3D&cpf=Aumg5Dq4bFxn1KE8ZWXYYw%3D%3D&plano=ypEyjiJVB9w%3D&url=p1FVim3WX1Y%3D',
            'serviceTransaction' => '201903180842193291-0',
            'customer' =>
                array (
                    'cpf' => '00000002054',
                    'firstName' => 'Mariana',
                    'lastName' => 'Silvestre',
                ),
        );
    }

    private function mockClientBuilder(): void
    {
        $this->app->bind(ClientBuilder::class, function () {
            $mock = Mockery::mock(ClientBuilder::class);
            $mock->shouldReceive('index')->once();
            return $mock;
        });
    }
}
