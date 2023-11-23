<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;
use Illuminate\Http\Response as IlluminateResponse;

class ViabilityMock implements Siv3ResponseMockInterface
{
    public static function make(): ViabilityMock
    {
        return new self();
    }

    public function getMock(Request $request): Response
    {
        $contents = json_decode($request->getBody()->getContents(), true);

        $body = (Arr::get($contents, 'cep') === Siv3TestBook::SUCCESS_POSTAL_CODE)
            ? json_encode(Siv3TestBook::SUCCESS_ADDRESS_BY_CPF)
            : json_encode(Siv3TestBook::FAILURE_ADDRESS_BY_CPF);

        return new Response(IlluminateResponse::HTTP_OK, [
            'Content-Type' => 'application/json'
        ], $body);
    }
}
