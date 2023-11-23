<?php

namespace NextelBR\Connection\NextelBR;

use TradeAppOne\Domain\HttpClients\Responseable;

class NextelBRConnection
{
    protected $nextelBRHttpClient;

    public function __construct(NextelBRHttpClient $nextelBRHttpClient)
    {
        $this->nextelBRHttpClient = $nextelBRHttpClient;
    }

    public function adhesion(string $protocol, array $adapted)
    {
        return $this->nextelBRHttpClient->put(NextelBRRoutes::adhesion($protocol), $adapted);
    }

    public function preAdhesion(string $protocol, array $adapted)
    {
        return $this->nextelBRHttpClient->put(NextelBRRoutes::preAdhesion($protocol), $adapted);
    }

    public function banks(): Responseable
    {
        return $this->nextelBRHttpClient->get(NextelBRRoutes::banks());
    }

    public function dueDates(): Responseable
    {
        return $this->nextelBRHttpClient->get(NextelBRRoutes::paymentDates());
    }

    public function fromOperator(): Responseable
    {
        return $this->nextelBRHttpClient->get(NextelBRRoutes::portabilityOperators());
    }

    public function eligibility(array $adapted)
    {
        return $this->nextelBRHttpClient->post(NextelBRRoutes::eligibility(), $adapted);
    }

    public function portabilityDates()
    {
        return $this->nextelBRHttpClient->get(NextelBRRoutes::portabilityDates());
    }

    public function getPlans(string $areaCode, string $score)
    {
        return $this->nextelBRHttpClient->get(
            NextelBRRoutes::products(),
            ['ddd' => $areaCode, 'scoreDescricao' => $score]
        );
    }

    public function cep(string $cep)
    {
        return $this->nextelBRHttpClient->get(NextelBRRoutes::cep($cep));
    }

    public function validateBankData(array $data)
    {
        return $this->nextelBRHttpClient->post(NextelBRRoutes::validateBankData(), $data);
    }
}
