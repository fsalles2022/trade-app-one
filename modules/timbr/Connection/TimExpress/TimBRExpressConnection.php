<?php

namespace TimBR\Connection\TimExpress;

class TimBRExpressConnection
{
    protected $client;

    public function __construct(TimBRExpressHttpClient $client)
    {
        $this->client = $client;
    }

    public function customerSubscription(array $payload, string $eligibility)
    {
        return $this->client->put(TimBRExpressRoutes::SUBSCRIPTION . '/' . $eligibility, $payload);
    }

    public function cancelSubscription(string $eligibilityToken)
    {
        return $this->client->put(TimBRExpressRoutes::SUBSCRIPTION . '/' . $eligibilityToken . '/cancel', []);
    }
}
