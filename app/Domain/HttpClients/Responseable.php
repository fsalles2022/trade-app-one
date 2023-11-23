<?php

namespace TradeAppOne\Domain\HttpClients;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;

interface Responseable
{
    public static function success(Response $response);

    public static function failure(BadResponseException $exception);

    public function isSuccess(): bool;

    public function toArray(): array;

    public function getStatus();

    public function get(string $key = null, $default = null);
}
