<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class ExportSalesMock implements Siv3ResponseMockInterface
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
        switch ($requestDecoded) {
            case Siv3TestBook::DATE_SUCESS_EXPORT:
                return new Response(200, ['Content-Type' => 'application/json'], $this->exportSalesSuccess());
            case Siv3TestBook::DATE_FAILURE_EXPORT:
                return new Response(200, ['Content-Type' => 'application/json'], $this->exportSalesFailure());
            default:
                return new Response(200, ['Content-Type' => 'application/json'], '');
        }
    }

    private function exportSalesSuccess(): ?string
    {
        return json_encode(Siv3TestBook::SALES_EXPORTABLE);
    }

    private function exportSalesFailure(): ?string
    {
        return json_encode(Siv3TestBook::NON_EXISTENTS_SALES_EXPORTABLE);
    }
}
