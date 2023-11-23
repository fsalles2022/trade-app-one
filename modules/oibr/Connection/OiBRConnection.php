<?php

namespace OiBR\Connection;

use OiBR\Adapters\OiBRControleCartaoRequestAdapter;
use OiBR\Adapters\OiBRRequestAdapter;
use OiBR\Connection\ElDoradoGateway\ElDoradoHttpClient;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Models\Collections\Service;

class OiBRConnection
{
    protected $httpClient;
    protected $elDorado;

    public function __construct(OiBRHttpClient $httpClient, ElDoradoHttpClient $elDorado)
    {
        $this->httpClient = $httpClient;
        $this->elDorado   = $elDorado;
    }

    public function controleCartaoStatus(string $uuid)
    {
        $route = OiBRRoutes::getControleCartaoStatus($uuid);
        return $this->httpClient->get($route, [], ['Content-Type' => 'application/json']);
    }

    public function controleCartaoSale(Service $service)
    {
        if ($service->mode == Modes::ACTIVATION) {
            return $this->controleCartaoSaleActivation($service);
        }
        if ($service->mode == Modes::MIGRATION) {
            $adapted = OiBRRequestAdapter::adapt($service);
            $route   = OiBRRoutes::postControleCartaoMigration();
        }
        return $this->httpClient->post($route, $adapted);
    }

    public function controleCartaoSaleActivation(Service $service)
    {
        $uuid    = data_get($service, 'operatorIdentifiers.uuid');
        $adapted = OiBRControleCartaoRequestAdapter::adapt($service);
        $route   = OiBRRoutes::postControleCartaoActivation($uuid);
        return $this->httpClient->post($route, $adapted);
    }

    public function controleBoletoSale(Service $service)
    {
        $adapted = OiBRRequestAdapter::adapt($service);
        $route   = OiBRRoutes::postControleBoleto($service->msisdn);
        return $this->httpClient->post($route, $adapted);
    }

    public function getPlans(string $pointOfSale, string $areaCode, string $invoiceType)
    {
        $query = ['estabelecimento' => $pointOfSale, 'localidade' => $areaCode];
        return $this->httpClient->get(OiBRRoutes::getPlans($invoiceType), $query);
    }

    public function registerCreditCard(string $number, string $year, string $month)
    {
        $body = ['pan' => $number, 'month' => $month, 'year' => $year];
        if (app()->environment() == 'production') {
            return $this->elDorado->postFormParams(OiBRRoutes::REGISTER_CREDIT_CARD_PROD, $body);
        } else {
            return $this->httpClient->postFormParams(OiBRRoutes::REGISTER_CREDIT_CARD, $body);
        }
    }

    public function controleBoletoEligibility(string $msisdn = '')
    {
        return $this->httpClient->get(OiBRRoutes::controleBoletoEligibility($msisdn));
    }

    public function controleCartaoEligibility(string $msisdn = '')
    {
        return $this->httpClient->get(OiBRRoutes::controleCartaEligibility($msisdn));
    }

    public function controleBoletoQuery(string $msisdn)
    {
        return $this->httpClient->get(OiBRRoutes::queryControleBoletoStatus($msisdn));
    }
}
