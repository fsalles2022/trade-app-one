<?php

namespace NextelBR\Assistance;

use Illuminate\Support\Facades\Cache;
use NextelBR\Adapters\Request\PreAdhesionRequestAdapter;
use NextelBR\Adapters\Response\PreAdhesionResponseAdapter;
use NextelBR\Assistance\OperationAssistances\NextelBRAssistancesFactory;
use NextelBR\Connection\NextelBR\NextelBRConnection;
use NextelBR\Enumerators\NextelBRCaches;
use NextelBR\Exceptions\EligibilityNotFound;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Services\SaleService;

class NextelBRSaleAssistance implements AssistanceBehavior
{
    const INDEX_OF_MSISDN = 'msisdnReservado';
    protected $connection;
    protected $saleService;

    public function __construct(SaleService $service, NextelBRConnection $connection)
    {
        $this->connection  = $connection;
        $this->saleService = $service;
    }

    public function integrateService(Service $service, array $payload = [])
    {
        $assistance = NextelBRAssistancesFactory::make($service->operation);
        if (in_array($service->status, [ServiceStatus::SUBMITTED])) {
            return $assistance->integrateService($service)->adapt();
        }
        $resultOfPreAdhesion = $this->preAdhesion($service);
        if ($resultOfPreAdhesion->isSuccess()) {
            $this->saleService->updateStatusService($service, ServiceStatus::SUBMITTED);
            $msisdn  = data_get($resultOfPreAdhesion->getAdapted(), self::INDEX_OF_MSISDN);
            $service = $this->saleService->updateService($service, ['msisdn' => $msisdn]);
            return $assistance->integrateService($service)->adapt();
        } else {
            $this->saleService->updateStatusService($service, ServiceStatus::REJECTED);
            return $resultOfPreAdhesion->adapt();
        }
    }

    public function preAdhesion(Service $service)
    {
        $customer           = data_get($service, 'customer');
        $cpf                = data_get($customer, 'cpf');
        $cachedInformations = Cache::get(NextelBRCaches::ELIGIBILITY . $cpf);
        if (is_null($cachedInformations)) {
            throw  new EligibilityNotFound();
        }
        $address           = $this->fillAddress($service);
        $adapted           = PreAdhesionRequestAdapter::adapt($service, compact('address', 'cachedInformations'));
        $protocol          = data_get($service->operatorIdentifiers, 'protocolo', '');
        $preAdhesionResult = $this->connection->preAdhesion($protocol, $adapted);
        $this->saleService->pushLogService($service, $preAdhesionResult->toArray());
        return new PreAdhesionResponseAdapter($preAdhesionResult);
    }

    private function fillAddress(Service $service)
    {
        $zipCode = data_get($service, 'customer.zipCode', '');
        $address = $this->connection->cep($zipCode)->toArray();
        return data_get($address, 'endereco', []);
    }
}
