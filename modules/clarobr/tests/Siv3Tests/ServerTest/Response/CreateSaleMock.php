<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;

class CreateSaleMock implements Siv3ResponseMockInterface
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

        switch (data_get($requestDecoded, 'msisdn', '')) {
            case substr(Siv3TestBook::MSISDN_SUCCESS, 2, MsisdnHelper::MIN_LENGTH):
                return new Response(201, ['Content-Type' => 'application/json'], $this->saleCreated());
            case substr(Siv3TestBook::MSISDN_FAILURE, 2, MsisdnHelper::MIN_LENGTH):
                return new Response(200, ['Content-Type' => 'application/json'], $this->saleNotCreated());
            default:
                return new Response(200, ['Content-Type' => 'application/json'], '');
        }
    }

    private function saleCreated(): ?string
    {
        return json_encode(Siv3TestBook::SALE_CREATED);
    }

    private function saleNotCreated(): ?string
    {
        return json_encode(Siv3TestBook::SALE_NOT_CREATED);
    }
}
