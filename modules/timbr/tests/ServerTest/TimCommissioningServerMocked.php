<?php

declare(strict_types=1);

namespace TimBR\Tests\ServerTest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use TimBR\Connection\TimPremiumCommissioning\TimCommissioningRoutes;
use TimBR\Tests\ServerTest\TimCommissioningResponses\SendSaleToCommissioningResponse;
use TimBR\Tests\ServerTest\TimCommissioningResponses\TimCommissioningResponseInterface;

class TimCommissioningServerMocked
{
    public const ROUTES = [
        TimCommissioningRoutes::SEND => SendSaleToCommissioningResponse::class
    ];

    public function __invoke(GuzzleRequest $req): FulfilledPromise
    {
        $path = $req->getUri()->getPath();

        $response = (array_key_exists($path, self::ROUTES))
            ? $this->getResponse($path, $req)
            : new Response(ResponseAlias::HTTP_NOT_FOUND, [], '{}');

        return new FulfilledPromise($response);
    }

    private function getResponse(string $path, RequestInterface $request): Response
    {
        return $this->makeClass($path)->getMock($request);
    }

    private function makeClass(string $path): ?TimCommissioningResponseInterface
    {
        $class = self::ROUTES[$path];

        return $class::make();
    }
}
