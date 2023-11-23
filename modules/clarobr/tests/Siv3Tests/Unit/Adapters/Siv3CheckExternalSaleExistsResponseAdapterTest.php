<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3\Siv3Tests\Unit\Adapters;

use ClaroBR\Adapters\Siv3CheckExternalSaleExistsResponseAdapter;
use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class Siv3CheckExternalSaleExistsResponseAdapterTest extends TestCase
{
    public function test_should_get_adapted_response_when_sale_exists(): void
    {
        $responseMock = new Response(
            ResponseAlias::HTTP_OK,
            [],
            json_encode(Siv3TestBook::SALE_EXISTENT)
        );

        $adapted = (new Siv3CheckExternalSaleExistsResponseAdapter(RestResponse::success($responseMock)))
            ->getAdapted();

        $this->assertArrayHasKey('message', $adapted);
        $this->assertEquals(
            trans('siv::messages.activation.check_sale_failed')['message'],
            data_get($adapted, 'message')
        );
    }

    public function test_should_get_adapted_response_when_sale_not_exists(): void
    {
        $responseMock = new Response(
            ResponseAlias::HTTP_OK,
            [],
            json_encode(Siv3TestBook::SALE_NON_EXISTENT)
        );
        $adapted      = (new Siv3CheckExternalSaleExistsResponseAdapter(RestResponse::success($responseMock)))
            ->getAdapted();

        $this->assertArrayHasKey('saleExists', $adapted);
        $this->assertArrayHasKey('saleId', $adapted);

        $this->assertEquals(Siv3TestBook::SALE_NON_EXISTENT['saleExists'], data_get($adapted, 'saleExists'));
        $this->assertEquals(Siv3TestBook::SALE_NON_EXISTENT['saleId'], data_get($adapted, 'saleId'));
    }
}
