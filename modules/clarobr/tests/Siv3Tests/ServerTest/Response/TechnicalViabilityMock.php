<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Arr;

class TechnicalViabilityMock implements Siv3ResponseMockInterface
{
    public static function make(): self
    {
        return new self();
    }

    public function getMock(Request $request): Response
    {
        $contents = json_decode($request->getBody()->getContents());

        $body = (Arr::get($contents, 'addressId') === Siv3TestBook::SUCCESS_ADDRESS_ID)
            ? json_encode(Siv3TestBook::SUCCESS_VIABILITY)
            : '{}';

        return new Response(IlluminateResponse::HTTP_OK, [
            'Content-Type' => 'application/json'
        ], $body);
    }
}
