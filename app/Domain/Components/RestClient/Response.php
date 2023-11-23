<?php


namespace TradeAppOne\Domain\Components\RestClient;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

interface Response
{
    public static function createFromResponse(ResponseInterface $response);

    public static function createFromClientException(ClientException $e);

    public function getMessage();

    public function getData();

    public function getBody();

    public function asArray($key = null);

    public function asJson();

    public function getResponse();
}
