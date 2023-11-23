<?php

namespace NextelBR\Tests\ServerTest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;

class NextelBRModalServerMock
{
    public function __invoke(RequestInterface $req, array $options)
    {
        $path   = $req->getUri()->getPath();
        $params = $req->getBody()->getContents();


        $success = file_get_contents(__DIR__ . '/responses/modal/success.json');
        $body    = stream_for($success);

        return new FulfilledPromise(
            new Response(200, ['Content­Type' => 'application/json'], $body)
        );
    }
}
