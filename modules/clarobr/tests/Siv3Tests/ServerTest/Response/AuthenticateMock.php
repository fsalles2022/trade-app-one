<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class AuthenticateMock implements Siv3ResponseMockInterface
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
            'token'           => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjEsImV4cGlyYXRpb25EYXRlIjoiMjAyMS0wOS0wOSAxOTo0NzowNCJ9.Hul9ooBdNkUQd01xOXHBzxJAWxXYLNH1p-Fv1J3WGdo',
            'expirationDate'  => '2021-09-10 01:47:04',
        ]);
    }
}
