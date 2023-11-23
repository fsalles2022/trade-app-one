<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class SendAuthorizationCodeMock implements Siv3ResponseMockInterface
{
    public static function make(): self
    {
        return new self();
    }

    public function getMock(Request $request): Response
    {
        $phoneNumber = json_decode($request->getBody()->getContents(), true)['phoneNumber'] ?? null;

        if ($phoneNumber === Siv3TestBook::PHONE_NUMBER_SUCCESS) {
            return new Response(200, [], '{"success": true}');
        }

        return new Response(500, [], '');
    }
}
