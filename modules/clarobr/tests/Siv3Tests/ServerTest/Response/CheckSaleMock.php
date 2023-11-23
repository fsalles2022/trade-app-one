<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class CheckSaleMock implements Siv3ResponseMockInterface
{
    /** @inheritDoc */
    public static function make(): self
    {
        return new self();
    }

    /** @inheritDoc */
    public function getMock(Request $request): Response
    {
        $requestDecoded = json_decode($request->getBody()->getContents(), true);

        switch (data_get($requestDecoded, 'customerCpf', 0)) {
            case Siv3TestBook::NON_EXISTENT_CUSTOMER_SALE:
                return new Response(200, ['Content-Type' => 'application/json'], $this->saleNotExistsBody());
            case Siv3TestBook::EXISTENT_CUSTOMER_SALE:
                return new Response(412, ['Content-Type' => 'application/json'], $this->saleExistsBody());
            default:
                return new Response(200, ['Content-Type' => 'application/json'], '');
        }
    }

    protected function saleNotExistsBody(): string
    {
        return json_encode(Siv3TestBook::SALE_NON_EXISTENT);
    }

    protected function saleExistsBody(): string
    {
        return json_encode(Siv3TestBook::SALE_EXISTENT);
    }
}
