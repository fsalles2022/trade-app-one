<?php

declare(strict_types=1);

namespace Tradehub\Tests\ServerTest\Response;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tradehub\Tests\TradeHubTestBook;

class SendTokenPortabilityMock implements TradeHubResponseMockInterface
{
    public static function make(): self
    {
        return new self();
    }

    public function getMock(Request $request): Response
    {
        $phoneNumber = json_decode($request->getBody()->getContents(), true)['phone'] ?? null;
        if ($phoneNumber === TradeHubTestBook::PHONE_NUMBER_SUCCESS) {
            return new Response(200, [], '{"success": true}');
        }

        return new Response(500, [], '');
    }
}
