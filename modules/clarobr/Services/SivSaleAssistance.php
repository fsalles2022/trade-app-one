<?php

namespace ClaroBR\Services;

use ClaroBR\Adapters\LogRequestAdapter;
use ClaroBR\Adapters\SivResponseAdapter;
use ClaroBR\Adapters\TradeAppToSivAdapter;
use ClaroBR\Connection\SivConnectionInterface;
use ClaroBR\Exceptions\ClaroExceptions;
use ClaroBR\OperationAssistances\ClaroAssistants;
use TradeAppOne\Domain\Enumerators\ConfirmOperationStatus;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHooksFactory;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;

class SivSaleAssistance implements AssistanceBehavior
{
    protected $connection;
    protected $saleRepository;

    public function __construct(
        SivConnectionInterface $sivConnection,
        SaleRepository $saleRepository
    ) {
        $this->connection     = $sivConnection;
        $this->saleRepository = $saleRepository;
    }

    /** @throws */
    public function integrateService(Service $service, array $payload = [])
    {
        if ($service->needToBeIntegreted()) {
            $integratedService = $this->integrate($service, $payload);
            if (! $integratedService instanceof Service) {
                return $integratedService;
            }
        }

        $serviceUpdated = $this->updateServiceSiv($service, $payload);
        return $this->activate($serviceUpdated, $payload);
    }

    public function integrate(Service $service, array $payload = [])
    {
        if ($invoiceType = data_get($payload, 'invoiceType')) {
            $this->saleRepository->updateService($service, [
               'invoiceType' => $invoiceType
            ]);
        }

        $adapted = TradeAppToSivAdapter::adapt($service);

        $services  = $adapted['service'];
        $customer  = $adapted['customer'];
        $saleUser  = $service->sale->user;
        $sivResult = $this->connection->sale($customer, $services, $saleUser);

        try {
            $sivResult                 = $sivResult->toArray()['data']['venda']['services'][0];
            $identifiers['venda_id']   = $sivResult['venda_id'];
            $identifiers['servico_id'] = $sivResult['id'];

            $service = $this->saleRepository->updateService($service, [
                    'operatorIdentifiers' => $identifiers,
                    'status'              => ServiceStatus::SUBMITTED
                ]);

            return $service;
        } catch (\Exception $exception) {
            $adapted = new SivResponseAdapter($sivResult);
            return $adapted->adapt();
        }
    }

    public function activate(Service $service, array $payload = [])
    {
        throw_if(is_null($service->operatorIdentifiers), new ServiceNotIntegrated());
        $integratedService = ClaroAssistants::make($service);
        return $integratedService->activate($service, $payload);
    }

    public function logSale(array $payload): bool
    {
        $service = $this->saleRepository->findInSale($payload['serviceTransaction']);
        $adapted = LogRequestAdapter::adapt($service, $payload);
        $vendaId = data_get($service->operatorIdentifiers, 'venda_id', null);

        throw_if($vendaId === null, new ServiceNotIntegrated());

        $response = $this->connection->logSale($adapted, $vendaId)->toArray();
        $this->saleRepository->pushLogService($service, $payload);

        $status = data_get($payload, 'status');

        if ($status === ConfirmOperationStatus::SUCCESS) {
            $serviceUpdated = $this->saleRepository->updateService($service, ['status' => ServiceStatus::APPROVED]);
            if ($serviceUpdated) {
                NetworkHooksFactory::run($serviceUpdated);
            }
            return true;
        }
        if ($status === ConfirmOperationStatus::FAILED) {
            $this->saleRepository->updateService($service, ['status' => ServiceStatus::REJECTED]);
            $this->saleRepository->pushLogService($service, $response);
            return false;
        }

        return true;
    }

    public function updateServiceSiv(Service $service, array $payload): Service
    {
        $keysToUpdate = [
            'iccid' => null,
            'invoiceType' => null
        ];

        $toUpdate = array_intersect_key($payload, $keysToUpdate);

        if (empty($toUpdate)) {
            return $service;
        }

        foreach ($toUpdate as $key => $value) {
            if ($service->$key === $value) {
                return $service;
            }
        }

        $serviceId = data_get($service, 'operatorIdentifiers.servico_id');
        $vendaId   = data_get($service, 'operatorIdentifiers.venda_id');

        $received = $this->connection->update($vendaId, $serviceId, $toUpdate);

        throw_unless($received->isSuccess(), ClaroExceptions::updateError($received->get()));

        return $this->saleRepository->updateService($service, $toUpdate);
    }
}
