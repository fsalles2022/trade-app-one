<?php

namespace TradeAppOne\Domain\Components\CEPClient;

use GuzzleHttp\Client;

class ViaCepHttpClient
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(['uri' => env('VIA_CEP')]);
    }
}
