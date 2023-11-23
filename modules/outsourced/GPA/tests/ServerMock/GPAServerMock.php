<?php


namespace Outsourced\GPA\tests\ServerMock;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response as PSR7Response;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Outsourced\GPA\Connections\GPARoutes;
use Outsourced\GPA\tests\Helpers\GPATestBook;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

class GPAServerMock
{
    public function __invoke(RequestInterface $request, array $opttion)
    {
        $path     = $request->getUri()->getPath();
        $response = Arr::get($this->responses($request), $path, []);
        $key      = key($response);
        $body     = Arr::get($response, $key, []);

        return new FulfilledPromise(new PSR7Response($key, ['Content­Type' => 'application/json'], stream_for($body)));
    }

    private function responses(RequestInterface $request): array
    {
        return [
            GPARoutes::SALE_REGISTER => $this->registerSale($request),
            GPARoutes::AUTH => $this->auth($request)
        ];
    }

    private function auth(RequestInterface $request): array
    {
        $key = $request->getHeaderLine('password') === GPATestBook::AUTH_PASSWORD ? 0 : 1;

        return [
            [Response::HTTP_OK => file_get_contents(__DIR__ . '/response/authAccessToken.json', true)],
            [Response::HTTP_UNAUTHORIZED => file_get_contents(__DIR__ . '/response/authInvalidCredentials.json', true)]
        ][$key];
    }

    private function registerSale(RequestInterface $request): array
    {
        $parameters  = json_decode($request->getBody()->getContents(), true);
        $customerCpf = Arr::get($parameters, 'cliente.cpf');
        $key         = $customerCpf === GPATestBook::SUCCESS_CUSTOMER ? 0 : 1;

        return [
            [Response::HTTP_CREATED => file_get_contents(__DIR__ . '/response/registerSale.json')],
            [Response::HTTP_UNPROCESSABLE_ENTITY => file_get_contents(__DIR__ . '/response/cannotProcess.json')],
        ][$key];
    }
}
