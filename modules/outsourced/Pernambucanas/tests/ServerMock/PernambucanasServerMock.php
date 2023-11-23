<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\tests\ServerMock;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use Outsourced\Pernambucanas\Connections\PernambucanasRoutes;
use Outsourced\Pernambucanas\tests\ServerMock\Apis\PernambucanasApiMockInterface;
use Outsourced\Pernambucanas\tests\ServerMock\Apis\SaleRegisterMock;
use Psr\Http\Message\RequestInterface;

class PernambucanasServerMock
{
    private const ROUTES = [
        PernambucanasRoutes::SALE_REGISTER => SaleRegisterMock::class,
    ];

    public function __invoke(RequestInterface $request): FulfilledPromise
    {
        $uri = $request->getRequestTarget();

        if (! array_key_exists($uri, self::ROUTES)) {
            return new FulfilledPromise(
                new Response(HttpResponse::HTTP_NOT_FOUND, [], '{}')
            );
        }

        return new FulfilledPromise($this->getResponseMockByPathAndRequest($uri, $request));
    }

    private function getResponseMockByPathAndRequest(string $path, RequestInterface $request): Response
    {
        return $this->getMockByPath($path)->getMock($request);
    }

    private function getMockByPath(string $path): ?PernambucanasApiMockInterface
    {
        $class = self::ROUTES[$path];

        if (class_exists($class)) {
            return $class::make();
        }

        return null;
    }
}
