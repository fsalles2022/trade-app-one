<?php

declare(strict_types=1);

namespace Tradehub\Tests\ServerTest\Response;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tradehub\Tests\TradeHubTestBook;

class CheckTokenPortabilityMock implements TradeHubResponseMockInterface
{
    public static function make(): self
    {
        return new self();
    }

    public function getMock(Request $request): Response
    {
        $code = json_decode($request->getBody()->getContents(), true)['code'] ?? null;

        if ($code === TradeHubTestBook::CODE_TOKEN_PORTABILITY_SUCCESS) {
            return new Response(200, [], '{"success": true, "error": false, "response": {"validated": true}}');
        }

        if ($code === TradeHubTestBook::CODE_TOKEN_PORTABILITY_EXCEPTION) {
            return new Response(500, [], '{}');
        }

        return new Response(200, [], '{"success": false}');
    }
}
