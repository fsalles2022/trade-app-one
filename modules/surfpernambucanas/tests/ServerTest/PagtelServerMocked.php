<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\ServerTest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\Response as HttpResponse;
use Psr\Http\Message\RequestInterface;
use SurfPernambucanas\Connection\PagtelRoutes;
use SurfPernambucanas\Tests\ServerTest\Apis\ActivationsApiMock;
use SurfPernambucanas\Tests\ServerTest\Apis\AddCardApiMock;
use SurfPernambucanas\Tests\ServerTest\Apis\AllocatedMsisdnApiMock;
use SurfPernambucanas\Tests\ServerTest\Apis\AuthenticateApiMock;
use SurfPernambucanas\Tests\ServerTest\Apis\GetCardApiMock;
use SurfPernambucanas\Tests\ServerTest\Apis\GetValuesApiMock;
use SurfPernambucanas\Tests\ServerTest\Apis\PagtelApiMockInterface;
use SurfPernambucanas\Tests\ServerTest\Apis\PlansApiMock;
use SurfPernambucanas\Tests\ServerTest\Apis\RechargeApiMock;
use SurfPernambucanas\Tests\ServerTest\Apis\SubmitPortinApiMock;
use SurfPernambucanas\Tests\ServerTest\Apis\SubscriberActivateApiMock;

class PagtelServerMocked
{
    public const ROUTES = [
        PagtelRoutes::AUTHENTICATE        => AuthenticateApiMock::class,
        PagtelRoutes::SUBSCRIBER_ACTIVATE => SubscriberActivateApiMock::class,
        PagtelRoutes::ALLOCATED_MSISDN    => AllocatedMsisdnApiMock::class,
        PagtelRoutes::GET_VALUES          => GetValuesApiMock::class,
        PagtelRoutes::GET_CARD            => GetCardApiMock::class,
        PagtelRoutes::ADD_CARD            => AddCardApiMock::class,
        PagtelRoutes::RECHARGE            => RechargeApiMock::class,
        PagtelRoutes::SUBMIT_PORTIN       => SubmitPortinApiMock::class,
        PagtelRoutes::PLANS               => PlansApiMock::class,
        PagtelRoutes::ACTIVATIONS         => ActivationsApiMock::class,
    ];

    /** @param mixed[] @options */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $path = $this->extractRealPathByRequestUri($request->getUri());

        if (array_key_exists($path, self::ROUTES) === false) {
            return new FulfilledPromise(
                new Response(HttpResponse::HTTP_NOT_FOUND, [], '{}')
            );
        }

        $response = $this->getResponseMockByPathAndRequest($path, $request);

        return new FulfilledPromise($response);
    }

    private function extractRealPathByRequestUri(Uri $uri): ?string
    {
        $baseUrl = config('integrations.pagtel.pernambucanas.uri', '');

        $baseUrlRequest = Uri::composeComponents(
            $uri->getScheme(),
            $uri->getAuthority(),
            $uri->getPath(),
            null,
            null
        );

        return str_replace($baseUrl, '', $baseUrlRequest);
    }

    private function getResponseMockByPathAndRequest(string $path, RequestInterface $request): Response
    {
        return $this->getMockByPath($path)->getMock($request);
    }

    private function getMockByPath(string $path): PagtelApiMockInterface
    {
        $class = self::ROUTES[$path];

        return $class::make();
    }
}
