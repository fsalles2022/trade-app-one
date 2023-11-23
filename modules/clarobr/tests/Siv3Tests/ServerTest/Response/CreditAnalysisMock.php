<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CreditAnalysisMock implements Siv3ResponseMockInterface
{
    public static function make(): self
    {
        return new self();
    }

    public function getMock(Request $request): Response
    {
        $contents = json_decode($request->getBody()->getContents(), true);

        $body = (Arr::get($contents, 'cpf') === Siv3TestBook::SUCCESS_CPF_CREDIT)
            ? json_encode(Siv3TestBook::SUCCESS_RESIDENTIL_CREDIT_ANALYSIS)
            : '{}';

        return new Response(ResponseAlias::HTTP_OK, [
            'Content-Type' => 'application/json'
        ], $body);
    }
}
