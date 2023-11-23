<?php

namespace TimBR\Connection\TimBRElDorado;

class TimBRElDoradoConnection
{
    protected $client;

    public function __construct(TimBRElDoradoHttpClient $client)
    {
        $this->client = $client;
    }

    public function registerCreditCard($number, $month, $year)
    {
        $body = ['pan' => $number, 'month' => $month, 'year' => $year];
        return $this->client->postFormParams(TimBRElDoradoRoutes::REGISTER_CREDIT_CARD_PROD, $body);
    }
}
