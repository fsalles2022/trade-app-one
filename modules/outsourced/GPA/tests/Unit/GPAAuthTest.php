<?php


namespace Outsourced\GPA\tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Http\Response;
use Outsourced\GPA\Connections\GPAHttpClient;
use Outsourced\GPA\Connections\GPARoutes;
use Outsourced\GPA\tests\Helpers\GPATestBook;
use Outsourced\GPA\tests\ServerMock\GPAServerMock;
use TradeAppOne\Tests\TestCase;

class GPAAuthTest extends TestCase
{
    protected $client;

    protected function setUp()
    {
        parent::setUp();
        $handlerMock  = new GPAServerMock();
        $handler      = HandlerStack::create($handlerMock);
        $this->client = (new Client(['handler' => $handler]));
    }

    /** @test */
    public function should_return_200_with_access_token(): void
    {
        $response = (new GPAHttpClient($this->client))->post(GPARoutes::AUTH, [], [
            'username' => GPATestBook::AUTH_USER_NAME,
            'password' => GPATestBook::AUTH_PASSWORD,
            'grant_type' =>  'password',
        ]);

        self::assertTrue($response->isSuccess());
        self::assertArrayHasKey('access_token', $response->toArray());
    }

    /** @test */
    public function should_return_401_when_invalid_credentials(): void
    {
        $response = (new GPAHttpClient($this->client))->post(GPARoutes::AUTH, [], [
            'username' => GPATestBook::AUTH_USER_NAME,
            'password' => GPATestBook::AUTH_PASSWORD . '#',
            'grant_type' =>  'password',
        ]);

        self::assertNotTrue($response->isSuccess());
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatus());
        self::assertArrayHasKey('error', $response->toArray());
    }
}
