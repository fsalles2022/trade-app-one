<?php

namespace NextelBR\Assistance\OperationAssistances;

use NextelBR\Adapters\Request\AdhesionRequestAdapter;
use NextelBR\Adapters\Response\AdhesionResponseAdapter;
use NextelBR\Connection\NextelBR\NextelBRConnection;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Services\SaleService;

class NextelBRControleBoletoAssistance implements AssistanceBehavior
{
    protected $connection;
    protected $saleService;

    public function __construct(NextelBRConnection $connection, SaleService $service)
    {
        $this->connection  = $connection;
        $this->saleService = $service;
    }

    public function integrateService(Service $service, array $payload = [])
    {
        $adapted          = AdhesionRequestAdapter::adapt($service);
        $protocolo        = data_get($service->operatorIdentifiers, 'protocolo');
        $resultOfAdhesion = $this->connection->adhesion($protocolo, $adapted);
        $this->saleService->pushLogService($service, $resultOfAdhesion->toArray());
        $responseAdapter = new AdhesionResponseAdapter($resultOfAdhesion);
        if ($responseAdapter->isSuccess()) {
            $this->saleService->updateStatusService($service, ServiceStatus::APPROVED);
            $msisdn = data_get($service, 'msisdn');
            $responseAdapter->pushAttributes(['msisdn' => $msisdn]);
        } else {
            $this->saleService->updateStatusService($service, ServiceStatus::REJECTED);
        }

        return $responseAdapter;
    }
}
