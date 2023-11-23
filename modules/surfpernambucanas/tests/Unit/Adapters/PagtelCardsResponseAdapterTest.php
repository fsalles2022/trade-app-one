<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Adapters;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use SurfPernambucanas\Adapters\PagtelCardsResponseAdapter;
use SurfPernambucanas\Enumerators\PagtelResponseCode;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class PagtelCardsResponseAdapterTest extends TestCase
{
    public function test_should_get_adapted_response(): void
    {
        $cardJson = '{"paymentType": "C", "paymentID": "1", "bin": "511111", "digFour": "5500", "expiration": "0725", "flag": "Mastercard"}';

        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            '{"code": "' . PagtelResponseCode::SUCCESS . '", "msg": "Sucesso", "cardList": [' . $cardJson . ']}'
        );

        $adapter = new PagtelCardsResponseAdapter(RestResponse::success($responseMock));

        $responseAdapted = $adapter->getAdapted();

        $dataToAssert = [
            "paymentType" => "C",
            "paymentId" => "1",
            "bin" => "511111",
            "digFour" => "5500",
            "expiration" => "0725",
            "flag" => "Mastercard"
        ];

        $this->assertEquals(PagtelResponseCode::SUCCESS, data_get($responseAdapted, 'code'));
        $this->assertEquals('Sucesso', data_get($responseAdapted, 'msg'));
        $this->assertEquals([$dataToAssert], data_get($responseAdapted, 'cards'));
    }
}
