<?php

namespace VivoTradeUp\Assistances;

use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHooksFactory;
use VivoTradeUp\Adapters\ControleFacilRequestAdapter;

class VivoTradeUpAssistance
{
    protected $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    public function activate(Service $service): array
    {
        $response = $this->m4uActivate($service);

        $this->logService($response, $service);

        return ['data' => ['urlM4U' => $response]];
    }

    public function m4uActivate(Service $service): ?string
    {
        $externalId = uniqid('', false);
        $urlM4U     = ControleFacilRequestAdapter::adapt($service, $externalId);

        if ($urlM4U !== null) {
            $this->saleRepository->updateService($service, [
                'operatorIdentifiers' => [
                    'idVenda' => $externalId
                ]
            ]);
        }
        return $urlM4U;
    }

    public function updateWithSuccess($response, Service $service, string $status = null): void
    {
        $this->saleRepository->updateService($service, [
            'status' => $status ?? ServiceStatus::ACCEPTED
        ]);
    }

    public function updateWithErrors($response, Service $service): void
    {
        $this->saleRepository->updateService($service, [
            'status' => ServiceStatus::REJECTED
        ]);
    }

    public function logService($response, Service $service): void
    {
        $this->saleRepository->pushLogService($service, ['urlM4U' => $response]);
    }

    public function confirmControleCartao(array $payload): bool
    {
        $successStatus      = 'SUCCESS';
        $serviceTransaction = data_get($payload, 'serviceTransaction');
        $service            = $this->saleRepository->findInSale($serviceTransaction);

        if ($service !== null) {
            $this->saleRepository->pushLogService($service, $payload);

            if (data_get($payload, 'status', 'FAILED') === $successStatus) {
                $this->saleRepository->updateService($service, ['status' => ServiceStatus::APPROVED]);
                NetworkHooksFactory::run($service);
            } else {
                $this->saleRepository->updateService($service, ['status' => ServiceStatus::REJECTED]);
            }
            return true;
        }
        return false;
    }
}
