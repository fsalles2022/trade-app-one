<?php

namespace VivoBR\Assistances;

use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use VivoBR\Adapters\Response\VivoBRSaleResponse;
use VivoBR\Adapters\SunServiceRequestAdapter;
use VivoBR\Connection\SunConnection;

class VivoBrAssistance
{
    protected $sunConnection;
    protected $saleRepository;

    public function __construct(SunConnection $sunConnection, SaleRepository $saleRepository)
    {
        $this->sunConnection  = $sunConnection;
        $this->saleRepository = $saleRepository;
    }

    public function activate(Service $service): VivoBRSaleResponse
    {
        $response = $this->sunActivate($service);

        $this->logService($response, $service);

        return $response;
    }

    public function sunActivate(Service $service): VivoBRSaleResponse
    {
        $network     = data_get($service->sale->pointOfSale, 'network.slug');
        $request     = SunServiceRequestAdapter::adapt($service);
        $sunResponse = $this->sunConnection
            ->selectCustomConnection($network)
            ->sale($request);

        return VivoBRSaleResponse::make($sunResponse);
    }

    public function updateWithSuccess(VivoBRSaleResponse $response, Service $service, string $status = null): void
    {
        $this->saleRepository->updateService($service, [
            'status' => $status ?? ServiceStatus::ACCEPTED
        ]);

        $response->pushAttributes($this->getSuccessMessage($response, $service));
    }

    public function updateWithErrors(VivoBRSaleResponse $response, Service $service): void
    {
        if ($response->shouldIgnoreErrors()) {
            return;
        }

        $this->saleRepository->updateService($service, [
            'status' => ServiceStatus::REJECTED
        ]);
    }

    public function logService(VivoBRSaleResponse $response, Service $service): void
    {
        $this->saleRepository->pushLogService($service, $response->getOriginal());

        $this->saleRepository->updateService($service, [
            'operatorIdentifiers' => $response->getIdentifiers()
        ]);
    }

    public function getSuccessMessage(VivoBRSaleResponse $response, Service $service): array
    {
        if ($response->usesBiometrics()) {
            return ['message' => trans('sun::messages.activation.with_biometrics')];
        }
        return ['message' => trans('sun::messages.activation.' . $service->operation)];
    }
}
