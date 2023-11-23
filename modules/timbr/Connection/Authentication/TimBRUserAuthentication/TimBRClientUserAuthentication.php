<?php

namespace TimBR\Connection\Authentication\TimBRUserAuthentication;

use GuzzleHttp\Client;
use TimBR\Connection\TimBRHttpClient;

class TimBRClientUserAuthentication
{
    public function build(array $config = []): TimBRHttpClient
    {
        $client = new Client($config);
        return new TimBRHttpClient($client);
    }
}
