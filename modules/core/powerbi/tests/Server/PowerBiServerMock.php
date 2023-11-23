<?php

namespace Core\PowerBi\tests\Server;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use function GuzzleHttp\Psr7\stream_for;

class PowerBiServerMock
{
    private const MICROSOFT_AUTH_PATH = 'common/oauth2/token';
    private const POWER_PI_TOKEN_PATH = 'v1.0/myorg/groups';

    public function __invoke(RequestInterface $request, array $options)
    {
        $path = $request->getUri()->getPath();

        return new FulfilledPromise(
            new Response(200, ['Content­Type' => 'application/json'], $this->getStream($path))
        );
    }

    private function getStream(string $path): StreamInterface
    {
        switch (true) {
            case $this->contains($path, self::MICROSOFT_AUTH_PATH):
                $response = file_get_contents(__DIR__ . '/Responses/microsoft_auth.json');
                break;

            case $this->contains($path, self::POWER_PI_TOKEN_PATH):
                $response = file_get_contents(__DIR__ . '/Responses/power_bi_access_token.json');
                break;

            default:
                $response =  '{}';
        }

        return stream_for($response);
    }

    private function contains($path, $find): bool
    {
        return strpos($path, $find) !== false;
    }
}
