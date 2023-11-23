<?php

declare(strict_types=1);

namespace Tradehub\Tests\ServerTest\Response;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class AuthenticateSellerMock implements TradeHubResponseMockInterface
{
    /** @inheritDoc */
    public static function make(): self
    {
        return new self();
    }

    /** @inheritDoc */
    public function getMock(Request $request): Response
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->successBody()
        );
    }

    protected function successBody(): string
    {
        return json_encode([
            "success" => true,
            "error" => false,
            "response" => [
                "saleOfServicesAuth" => [
                    "message" => "Autenticado com sucesso",
                    "token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHBpcmF0aW9uIjoiMjAyMi0xMC0wNSAxOTo1MTo1NSIsInVpZCI6ImV5SjBlWEFpT2lKS1YxUWlMQ0poYkdjaU9pSklVekkxTmlKOS5leUoxYzJWeUlqcDdJbWxrSWpveU5qUTJNVElzSW1sa1ZHOXJaVzRpT2lKbGVVb3daVmhCYVU5cFNrdFdNVkZwVEVOS2FHSkhZMmxQYVVwSlZYcEpNVTVwU2prdVpYbEtjRnBEU1RaTmFsa3dUbXBGZVdaUkxqSm9SbEIxV0U4d1dEQnVNbFZsYmkxS1pFTnVXSEU0ZW5KRlkwbGZVRVZxUVY5Rlp6WnNOVzFyYzI4aUxDSnliMnhsU1dRaU9qVTBMQ0ptYVhKemRFNWhiV1VpT2lKVVZWSkRUeUlzSW14aGMzUk9ZVzFsSWpvaVJFRWdWRlZTVVZWSlFTSXNJbVZ0WVdsc0lqb2lZbkoxYm04dWMyRnVkRzl6UUhSeVlXUmxkWEJuY205MWNDNWpiMjBpTENKamNHWWlPaUl6TlRBMk1qZzFPVGd3T0NJc0ltSnBjblJvWkdGMFpTSTZiblZzYkN3aVkzSmxZWFJsWkVGMElqb2lNakF5TWkwd05pMHhOU0F3Tmpvd01EbzFNQ0lzSW5Wd1pHRjBaV1JCZENJNklqSXdNakl0TURrdE16QWdNRGc2TVRBNk1qQWlMQ0prWld4bGRHVmtRWFFpT201MWJHeDlMQ0psZUhCcGNtRjBhVzl1SWpvaU1qQXlNaTB4TUMwd05TQXhPVG8xTVRvMU5TSjkuY1NaVkt4NklucmU4bTYtMDZhU0JsRXNuSVhWcDE1U1F1V2hDVk9QSm1PdyJ9.oO2qWrl1TXQygke9Dqjx8t4jtsXqpFEJFgz4q_FrZ-k",
                    "expirationDate" => "2022-10-05 19:51:55"
                ]
            ]
        ]);
    }
}
