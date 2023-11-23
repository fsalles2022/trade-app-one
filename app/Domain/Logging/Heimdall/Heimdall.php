<?php

namespace TradeAppOne\Domain\Logging\Heimdall;

interface Heimdall
{
    public function realm(string $realm): Heimdall;

    public function request($request): Heimdall;

    public function response($response): Heimdall;

    public function catchException($response): Heimdall;

    public function url(?string $url): Heimdall;

    public function httpClient($client): Heimdall;

    public function method(?string $method = ''): Heimdall;

    public function start($start): Heimdall;

    public function end($end): Heimdall;

    public function executionTime($end): Heimdall;

    public function fire();
}
