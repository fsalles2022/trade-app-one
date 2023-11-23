<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3\Siv3Tests\Unit\Adapters;

use ClaroBR\Adapters\Siv3CreateExternalSaleResponseAdapter;
use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class Siv3CreateExternalSaleResponseAdapterTest extends TestCase
{
    /** @test */
    public function method_should_return_success_and_sale_id_in_array_when_created(): void
    {
        $responseMock = new Response(
            ResponseAlias::HTTP_CREATED,
            [],
            json_encode(Siv3TestBook::SALE_NOT_CREATED)
        );

        $responseAdapted = (new Siv3CreateExternalSaleResponseAdapter(RestResponse::success($responseMock)))->getAdapted();

        $this->assertArrayHasKey('success', $responseAdapted);
        $this->assertArrayHasKey('saleId', $responseAdapted);
    }

    /** @test */
    public function method_should_return_message_when_sale_(): void
    {
        $responseMock = new Response(
            ResponseAlias::HTTP_OK,
            [],
            json_encode(Siv3TestBook::SALE_NOT_CREATED)
        );

        $responseAdapted = (new Siv3CreateExternalSaleResponseAdapter(RestResponse::success($responseMock)))->getAdapted();

        $this->assertArrayHasKey('message', $responseAdapted);
        $this->assertEquals(
            trans('siv::messages.activation.save_sale_failed')['message'],
            data_get($responseAdapted, 'message')
        );
    }
}
