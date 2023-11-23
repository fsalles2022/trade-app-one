<?php

declare(strict_types=1);

namespace Tradehub\Tests\ServerTest\Response;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class AuthenticateMock implements TradeHubResponseMockInterface
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
                "auth" => [
                    "token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHBpcmF0aW9uIjoiMjAyMi0xMC0wNSAxOTozNzozMyIsImNvbnRyb2xUb2tlbiI6ImV5SjBlWEFpT2lKS1YxUWlMQ0poYkdjaU9pSklVekkxTmlKOS5leUpwWkNJNk1pd2lZWEJwUzJWNUlqb2lNbUl4WmpJeVpURmlNV00yTTJRMU16Tm1ZemN6WlRneU9UZzBNRGRqWXpnaUxDSmxlSEJwY21GMGFXOXVJam9pTWpBeU1pMHhNQzB3TlNBeE9Ub3pOem96TXlKOS5NMzNCRDZBMDFubTYySnNabFlmYkliam1ydWRqUVhldnF3djFqejZjUUFjIn0.NZrjdal3ZvZlZ9wYdQ3A78AwLgVb4vvod18Zh_TJKuM",
                    "message" => "Autenticado com sucesso",
                    "expirationDate" => "2022-10-05 19:37:33"
                ]
            ]
        ]);
    }
}
