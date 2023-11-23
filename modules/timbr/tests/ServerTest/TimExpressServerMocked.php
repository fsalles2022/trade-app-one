<?php


namespace TimBR\Tests\ServerTest;

use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;
use TimBR\Connection\TimExpress\TimBRExpressRoutes;
use TimBR\Tests\TimBRTestBook;

class TimExpressServerMocked
{
    public function __invoke(RequestInterface $req, array $options)
    {
        $path                = $req->getUri()->getPath();
        $attributes          = json_decode($req->getBody()->getContents(), true);
        $subscriptionSuccess = file_get_contents(__DIR__ . '/TimExpressResponses/subscription.json');
        $subscriptionFailed  = file_get_contents(__DIR__ . '/TimExpressResponses/subscriptionFailed.json');

        if (str_contains($path, TimBRExpressRoutes::SUBSCRIPTION)) {
            $caseCpf = data_get($attributes, 'client.cpf');
            if ($caseCpf == TimBRTestBook::SUCCESS_CUSTOMER || TimBRTestBook::SUCCESS_CUSTOMER_EXPRESS) {
                $body = stream_for($subscriptionSuccess);
            } elseif ($caseCpf == TimBRTestBook::FAILURE_CUSTOMER_EXPRESS) {
                $body     = stream_for($subscriptionFailed);
                $response = new Response(406, ['Content­Type' => 'application/json'], $body);
                return new RejectedPromise(new ServerException('Server is down', $req, $response));
            } else {
                $body = stream_for($subscriptionSuccess);
            }
        } else {
            $body = stream_for($subscriptionFailed);
        }

        return new FulfilledPromise(
            new Response(200, ['Content­Type' => 'application/json'], $body)
        );
    }
}
