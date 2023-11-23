<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\Unit\Connection;

use ClaroBR\Connection\Siv3Connection;
use ClaroBR\Connection\Siv3HttpClient;
use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Tests\TestCase;

class Siv3ConnectionTest extends TestCase
{
    /** @var string[] $sale */
    private $sale = [
        'mode' => Modes::ACTIVATION,
        'areaCode' => '11',
        'msisdn' => '953555813',
        'iccid' => '89550000000000000000',
        'customerCpf' => '04155519199',
        'salesmanCpf' => '99911133323',
        'pointOfSaleCode' => 'XPTO',
        'networkSlug' => 'Claro'
    ];

    private $filters = [
        'startDate' => '2002-12-08',
        'endDate' => '2021-09-13',
        'pointOfSaleCode' => 'XPTO'
    ];

    public function test_should_authenticate_in_siv3(): void
    {
        $connection = $this->resolveSiv3Connection();
        $response   = $connection->authenticate();

        $this->assertTrue($response->isSuccess());

        $responseArray = $response->toArray();

        $this->assertArrayHasKey('token', $responseArray);
        $this->assertArrayHasKey('expirationDate', $responseArray);
    }

    public function test_should_get_token_return_access_token(): void
    {
        $connection = $this->resolveSiv3Connection();
        $this->assertNotEmpty($connection->getToken());
    }

    public function test_should_not_authenticated_siv3(): void
    {
        $this->mockHandlerClientWithResponse(
            new Response(
                HttpResponse::HTTP_OK,
                [],
                '{"authenticated": false}'
            )
        );

        $this->expectException(BuildExceptions::class);
        $this->resolveSiv3Connection()->authenticate();
    }

    public function test_should_return_success_check_sale_customer(): void
    {
        $sivConnection = $this->resolveSiv3Connection()->checkSale([
            'customerCpf'    => Siv3TestBook::EXISTENT_CUSTOMER_SALE
        ]);

        $this->assertEquals(HttpResponse::HTTP_PRECONDITION_FAILED, $sivConnection->getStatus());
        $this->assertJson($sivConnection->toJson(), '{"saleExists":true,"saleId":102030}');
    }

    public function test_should_return_failure_when_check_sale_customer_not_exist(): void
    {
        $sivConnection = $this->resolveSiv3Connection()->checkSale([
            'customerCpf'    => Siv3TestBook::NON_EXISTENT_CUSTOMER_SALE
        ]);

        $this->assertEquals(HttpResponse::HTTP_OK, $sivConnection->getStatus());
        $this->assertJson($sivConnection->toJson(), '{"saleExists":false,"saleId":0}');
    }

    public function test_should_return_status_201_created_and_sale_id_when_save_sale(): void
    {
        $sivConnection = $this->resolveSiv3Connection()->createSale($this->sale);

        $this->assertEquals(HttpResponse::HTTP_CREATED, $sivConnection->getStatus());
        $this->assertEquals(true, $sivConnection->get('success'));
    }

    public function test_should_return_sales_to_report(): void
    {
        $this->mockSiv3Connection('getSalesToReport', RestResponse::success(
            new Response(HttpResponse::HTTP_OK, [], json_encode(Siv3TestBook::SALES_EXPORTABLE))
        ));

        $siv3Connection = $this->resolveSiv3Connection();

        $response = $siv3Connection->getSalesToReport($this->filters);

        $this->assertSame(Siv3TestBook::SALES_EXPORTABLE, $response->toArray());
    }

    public function test_should_return_success_false_when_sale_not_registered(): void
    {
        $this->mockSiv3Connection('createSale', RestResponse::success(
            new Response(HttpResponse::HTTP_OK, [], '{"success": false, "saleId": 0}')
        ));

        $siv3Connection = $this->resolveSiv3Connection();

        $response = $siv3Connection->createSale($this->sale);

        $this->assertEquals(false, $response->get('success'));
        $this->assertSame(Siv3TestBook::SALE_NOT_CREATED, $response->toArray());
    }

    private function resolveSiv3Connection(): Siv3Connection
    {
        return resolve(Siv3Connection::class);
    }

    public function mockSiv3Connection($method, $response): void
    {
        $this->instance(
            Siv3Connection::class,
            Mockery::mock(Siv3Connection::class, function (MockInterface $mock) use ($method, $response): void {
                $mock->shouldReceive($method)
                    ->andReturn($response);
            })
        );
    }

    private function mockHandlerClientWithResponse(ResponseInterface ...$response): void
    {
        $this->app->bind(Siv3HttpClient::class, function () use ($response) {
            $mock    = new MockHandler($response);
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);

            return new Siv3HttpClient($client);
        });
    }
}
