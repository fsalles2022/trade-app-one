<?php

declare(strict_types=1);

namespace Tradehub\Tests\Unit\Connection;

use TradeAppOne\Tests\TestCase;
use Tradehub\Connection\TradeHubConnection;

class TradeHubConnectionTest extends TestCase
{

    /**
     * @var TradeHubConnection
     */
    private $connection;

    private $tradeHubResponse;

    private $tradeHubSellerResponse;

    private function authenticateTradehub()
    {
        $connection = $this->resolveTradeHubConnection();

        if ($this->tradeHubResponse == null) {
            return $this->tradeHubResponse = $connection->authenticate();
        }

        return $this->tradeHubResponse;
    }

    private function authenticateTradehubSeller()
    {
        $connection = $this->resolveTradeHubConnection();

        $token = $this->authenticateTradehub()->toArray();

        if ($this->tradeHubSellerResponse == null) {
            return $this->tradeHubSellerResponse = $connection->authenticateSeller($token['response']['auth']['token']);
        }

        return $this->tradeHubSellerResponse;
    }

    public function test_success_tradehub_auth_response_must_match_expected_payload(): void
    {
        $responseArray = $this->authenticateTradehub()->toArray();

        $this->assertArrayHasKey('success', $responseArray);
        $this->assertArrayHasKey('error', $responseArray);
        $this->assertArrayHasKey('response', $responseArray);
    }

    public function test_success_tradehub_seller_auth_response_must_match_expected_payload(): void
    {
        $responseSellerArray = $this->authenticateTradehubSeller()->toArray();

        $this->assertArrayHasKey('success', $responseSellerArray);
        $this->assertArrayHasKey('error', $responseSellerArray);
        $this->assertArrayHasKey('response', $responseSellerArray);
    }

    public function test_tradehub_auth(): void
    {
        $response = $this->authenticateTradehub();

        $this->assertTrue($response->isSuccess());
    }

    public function test_tradehub_seller_auth(): void
    {
        $response = $this->authenticateTradehubSeller();

        $this->assertTrue($response->isSuccess());
    }

    private function resolveTradeHubConnection(): TradeHubConnection
    {
        if ($this->connection == null) {
            $this->connection = resolve(TradeHubConnection::class);
        }

        return $this->connection;
    }
}
