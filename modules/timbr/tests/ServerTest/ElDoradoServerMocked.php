<?php

namespace TimBR\Tests\ServerTest;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\stream_for;
use TimBR\Connection\TimBRElDorado\TimBRElDoradoRoutes;
use TimBR\Tests\TimBRTestBook;

class ElDoradoServerMocked
{
    public function __invoke(RequestInterface $req, array $options)
    {
        $path                      = $req->getUri()->getPath();
        $attributes                = json_decode($req->getBody()->getContents(), true) ?? (string) $req->getBody();
        $registerCreditCardSuccess = file_get_contents(__DIR__ . '/M4uResponses/registerCreditCard.json');
        $registerCreditCardFailed  = file_get_contents(__DIR__ . '/M4uResponses/registerCreditCardFailed.json');

        if (is_string($attributes)) {
            parse_str($attributes, $attributes);
        }

        if ($path == TimBRElDoradoRoutes::REGISTER_CREDIT_CARD_PROD) {
            $caseCreditCard = data_get($attributes, 'pan');
            if ($caseCreditCard == TimBRTestBook::SUCCESS_CUSTOMER_EXPRESS_CC) {
                $body = stream_for($registerCreditCardSuccess);
            } else {
                $body     = stream_for($registerCreditCardFailed);
                $response = new Response(421, ['Content­Type' => 'application/json'], $body);
                return new RejectedPromise(new ClientException($body, $req, $response));
            }
        }

        return new FulfilledPromise(
            new Response(200, ['Content­Type' => 'application/json'], $body)
        );
    }
}
