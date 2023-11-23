<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class CheckAuthorizationCodeMock implements Siv3ResponseMockInterface
{
    public static function make(): self
    {
        return new self();
    }

    public function getMock(Request $request): Response
    {
        $code = json_decode($request->getBody()->getContents(), true)['codeAuthorization'] ?? null;

        if ($code === Siv3TestBook::CODE_AUTHORIZATION_SUCCESS) {
            return new Response(200, [], '{"success": true}');
        }

        if ($code === Siv3TestBook::CODE_AUTHORIZATION_EXCEPTION) {
            return new Response(500, [], '{}');
        }

        return new Response(200, [], '{"success": false}');
    }
}
