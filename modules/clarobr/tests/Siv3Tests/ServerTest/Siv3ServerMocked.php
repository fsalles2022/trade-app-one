<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\ServerTest;

use ClaroBR\Connection\Siv3Routes;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\AddressAddressMock;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\CheckAuthorizationCodeMock;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\SendAuthorizationCodeMock;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\ViabilityMock;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\AuthenticateMock;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\CheckSaleMock;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\CreditAnalysisMock;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\TechnicalViabilityMock;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\CreateSaleMock;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\ExportSalesMock;
use ClaroBR\Tests\Siv3Tests\ServerTest\Response\Siv3ResponseMockInterface;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Siv3ServerMocked
{
    public const SIV3_ROUTES = [
        Siv3Routes::ENDPOINT_AUTHENTICATE => AuthenticateMock::class,
        Siv3Routes::ENDPOINT_CHECK_EXTERNAL_SALE => CheckSaleMock::class,
        Siv3Routes::ENDPOINT_CREATE_EXTERNAL_SALE => CreateSaleMock::class,
        Siv3Routes::ENDPOINT_EXPORT_EXTERNAL_SALE => ExportSalesMock::class,
        Siv3Routes::ADDRESS_BY_POSTAL_CODE => ViabilityMock::class,
        Siv3Routes::ENDPOINT_SEND_AUTHORIZATION => SendAuthorizationCodeMock::class,
        Siv3Routes::ENDPOINT_CHECK_AUTHORIZATION => CheckAuthorizationCodeMock::class,
        Siv3Routes::TECHNICAL_VIABILITY => TechnicalViabilityMock::class,
        Siv3Routes::RESIDENTIAL_CREDIT_ANALYSIS => CreditAnalysisMock::class,
        Siv3Routes::ENDPOINT_ADDRESS => AddressAddressMock::class
    ];

    public function __invoke(GuzzleRequest $req): FulfilledPromise
    {
        $path = $req->getUri()->getPath();

        $response = (array_key_exists($path, self::SIV3_ROUTES))
            ? $this->getResponse($path, $req)
            : new Response(ResponseAlias::HTTP_NOT_FOUND, [], '{}');

        return new FulfilledPromise($response);
    }

    private function getResponse(string $path, RequestInterface $request): Response
    {
        return $this->makeClass($path)->getMock($request);
    }

    private function makeClass(string $path): ?Siv3ResponseMockInterface
    {
        $class = self::SIV3_ROUTES[$path];

        if (method_exists($class, 'make')) {
            return $class::make();
        }
        return null;
    }
}
