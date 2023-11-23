<?php


namespace Generali\tests\ServerMock;

use FontLib\Table\Type\post;
use Generali\Assistance\Connection\GeneraliRoutes;
use Generali\Connection\Authentication\AuthenticationRoutes;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

class GeneraliServerMock
{
    public const REFERENCE = "201912121941036750-0";

    public function __invoke(RequestInterface $request, array $opttion)
    {
        $path = $request->getUri()->getPath();
        $body = data_get($this->responses($request), $path, '');

        if ($path === GeneraliRoutes::activate()) {
            return new FulfilledPromise(
                new Response(201, ['Content­Type' => 'application/json'], stream_for($body))
            );
        }

        return new FulfilledPromise(new Response(200, ['Content­Type' => 'application/json'], stream_for($body)));
    }

    private function responses(RequestInterface $request): array
    {
        return [
            GeneraliRoutes::transactionByReference(self::REFERENCE) =>
                file_get_contents(__DIR__ . '/response/transaction.json'),
            GeneraliRoutes::eligibility() => file_get_contents(__DIR__ . '/response/eligibility.json'),
            GeneraliRoutes::calcPremium() => file_get_contents(__DIR__ . '/response/calcPremium.json'),
            GeneraliRoutes::activate()    => file_get_contents(__DIR__ . '/response/subscription.json'),
            GeneraliRoutes::calcRefund()  => file_get_contents(__DIR__ . '/response/calcRefund.json'),
            AuthenticationRoutes::AUTH    => file_get_contents(__DIR__ . '/response/NEO/neoSuccessAuth.json'),
            GeneraliRoutes::PRODUCT       => file_get_contents(__DIR__ . '/response/NEO/neoSuccessProducts.json'),
            GeneraliRoutes::PLAN          => $this->getResponsePlan($request)
        ];
    }

    private function getResponsePlan(RequestInterface $request)
    {
         return preg_replace('/\D/', '', $request->getUri()->getQuery()) === '132'
            ? file_get_contents(__DIR__ . '/response/NEO/neoSuccessPlan.json')
            : file_get_contents(__DIR__ . '/response/NEO/neoFailurePlan.json');
    }
}
