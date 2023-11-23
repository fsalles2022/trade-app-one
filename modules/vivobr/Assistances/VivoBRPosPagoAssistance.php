<?php

namespace VivoBR\Assistances;

use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use VivoBR\Adapters\SunResponseAdapter;
use VivoBR\Adapters\SunServiceRequestAdapter;
use VivoBR\Connection\SunConnection;

class VivoBRPosPagoAssistance extends VivoBrAssistance implements AssistanceBehavior
{
    public function integrateService(Service $service, array $payload = [])
    {
        $response = $this->activate($service);

        $response->isSuccess()
            ? $this->updateWithSuccess($response, $service)
            : $this->updateWithErrors($response, $service);

        return $response->adapt();
    }
}
