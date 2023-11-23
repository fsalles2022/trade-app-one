<?php

declare(strict_types=1);

namespace TimBR\Tests\ServerTest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use TimBR\Connection\BrScan\BrScanRoutes;
use TimBR\Tests\ServerTest\TimBrScanResponses\AuthenticateStatusResponse;
use TimBR\Tests\ServerTest\TimBrScanResponses\GenerateAuthenticateLinkResponse;
use TimBR\Tests\ServerTest\TimBrScanResponses\SaleTermStatusResponse;
use TimBR\Tests\ServerTest\TimBrScanResponses\SendSaleForTermSignatureResponse;
use TimBR\Tests\ServerTest\TimBrScanResponses\SendWelcomeKitForCustomerResponse;
use TimBR\Tests\ServerTest\TimBrScanResponses\TimBrScanResponseInterface;

class TimBrScanServerMocked
{
    public const ROUTES = [
        BrScanRoutes::GENERATE_AUTHENTICATE_LINK    => GenerateAuthenticateLinkResponse::class,
        BrScanRoutes::AUTHENTICATE_STATUS           => AuthenticateStatusResponse::class,
        BrScanRoutes::SEND_SALE_FOR_TERM_SIGNATURE  => SendSaleForTermSignatureResponse::class,
        BrScanRoutes::TERM_SIGNATURE_STATUS         => SaleTermStatusResponse::class,
        BrScanRoutes::SEND_WELCOME_KIT_FOR_CUSTOMER => SendWelcomeKitForCustomerResponse::class,
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

    private function makeClass(string $path): ?TimBrScanResponseInterface
    {
        $class = self::ROUTES[$path];

        return $class::make();
    }
}
