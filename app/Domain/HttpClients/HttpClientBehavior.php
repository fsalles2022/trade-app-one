<?php

namespace TradeAppOne\Domain\HttpClients;

interface HttpClientBehavior
{
    public function get(string $url, array $query = [], array $headers = []): Responseable;

    public function post(string $url, array $body = [], array $headers = []): Responseable;

    public function put(string $url, array $body = [], array $headers = []): Responseable;

    public function delete(string $url, array $body = [], array $headers = []): Responseable;

    public function postFormParams(string $url = '', array $body = [], array $headers = []): Responseable;
}
