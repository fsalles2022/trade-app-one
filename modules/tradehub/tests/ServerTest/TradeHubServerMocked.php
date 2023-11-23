<?php

declare(strict_types=1);

namespace Tradehub\Tests\ServerTest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tradehub\Connection\TradeHubRoutes;
use Tradehub\Tests\ServerTest\Response\AuthenticateMock;
use Tradehub\Tests\ServerTest\Response\AuthenticateSellerMock;
use Tradehub\Tests\ServerTest\Response\CheckTokenPortabilityMock;
use Tradehub\Tests\ServerTest\Response\SendTokenPortabilityMock;
use Tradehub\Tests\ServerTest\Response\TradeHubResponseMockInterface;

class TradeHubServerMocked
{
    public const TRADEHUB_ROUTES = [
        TradeHubRoutes::ENDPOINT_AUTHENTICATE => AuthenticateMock::class,
        TradeHubRoutes::ENDPOINT_AUTHENTICATE_SELLER => AuthenticateSellerMock::class,
        TradeHubRoutes::ENDPOINT_SEND_TOKEN_PORTABILITY     => SendTokenPortabilityMock::class,
        TradeHubRoutes::ENDPOINT_VALIDATE_TOKEN_PORTABILITY => CheckTokenPortabilityMock::class
    ];

    public function __invoke(GuzzleRequest $req): FulfilledPromise
    {
        $path = $req->getUri()->getPath();

        $response = (array_key_exists($path, self::TRADEHUB_ROUTES))
            ? $this->getResponse($path, $req)
            : new Response(ResponseAlias::HTTP_NOT_FOUND, [], '{}');

        return new FulfilledPromise($response);
    }

    private function getResponse(string $path, RequestInterface $request): Response
    {
        return $this->makeClass($path)->getMock($request);
    }

    private function makeClass(string $path): ?TradeHubResponseMockInterface
    {
        $class = self::TRADEHUB_ROUTES[$path];

        return $class::make();
    }
}
