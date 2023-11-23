<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest\Response;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class AddressAddressMock implements Siv3ResponseMockInterface
{
    public static function make(): self
    {
        return new self();
    }

    public function getMock(Request $request): Response
    {
        $requestDecoded = json_decode($request->getBody()->getContents(), true);

        if (data_get($requestDecoded, 'postalCode', 0) !== $this->getCep()) {
            return $this->getResponseAddress();
        }

        return new Response(200, ['Content-Type' => 'application/json'], '{}');
    }

    private function getResponseAddress(): Response
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            '[{"id":980730,"streetName":"Rua Bonnard","postalCode":"06465-134","neighborhoodId":21,"neighborhood":
            "Alphaville Empresarial","cityId":1057,"city":"Barueri","ibge":"3505708","ddd":11,"stateId":16,"state":"SP",
            "externalData":{"claroCityIdOperatorCode":"443","claroCityIdForFilterPlan":"5443"}}]'
        );
    }

    private function getCep(): string
    {
        return (string) str_replace('-', '', Siv3TestBook::ZIP_CODE_NOT_FOUND_ADDRESS);
    }
}
