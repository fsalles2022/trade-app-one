<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3\Siv3Tests\Unit\Adapters;

use ClaroBR\Adapters\Siv3ReportExternalSaleResponseAdapter;
use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class Siv3ReportExternalSaleResponseAdapterTest extends TestCase
{
    /** @return mixed[] */
    public static function structureSale(): array
    {
        return Siv3TestBook::SALES_EXPORTABLE['data'][0];
    }


    /** @test */
    public function method_should_return_array_of_external_sale()
    {
        $responseMock = new Response(
            ResponseAlias::HTTP_CREATED,
            [],
            json_encode(Siv3TestBook::SALES_EXPORTABLE)
        );

        $responseAdapted = (new Siv3ReportExternalSaleResponseAdapter(RestResponse::success($responseMock)))->getAdapted();

        $this->assertEquals(self::structureSale(), $responseAdapted[0]);
    }

    /** @test */
    public function method_should_return_empty_array(): void
    {
        $responseMock = new Response(
            ResponseAlias::HTTP_OK,
            [],
            json_encode(Siv3TestBook::NON_EXISTENTS_SALES_EXPORTABLE)
        );

        $responseAdapted = (new Siv3ReportExternalSaleResponseAdapter(RestResponse::success($responseMock)))->getAdapted();

        $this->assertEmpty(data_get($responseAdapted, 'mode'));
        $this->assertEmpty(data_get($responseAdapted, 'areaCode'));
        $this->assertEmpty(data_get($responseAdapted, 'msisdn'));
        $this->assertEmpty(data_get($responseAdapted, 'iccid'));
        $this->assertEmpty(data_get($responseAdapted, 'customerCpf'));
        $this->assertEmpty(data_get($responseAdapted, 'salesmanCpf'));
        $this->assertEmpty(data_get($responseAdapted, 'pointOfSaleCode'));
        $this->assertEmpty(data_get($responseAdapted, 'networkSlug'));
    }
}
