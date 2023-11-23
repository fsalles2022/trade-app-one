<?php

namespace VivoBR\Assistances;

use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\AssistanceBehavior;

class VivoBRPreAssistance extends VivoBrAssistance implements AssistanceBehavior
{
    public function integrateService(Service $service, array $payload = [])
    {
        $response = $this->activate($service);

        $response->isSuccess()
            ? $this->updateWithSuccess($response, $service, ServiceStatus::APPROVED)
            : $this->updateWithErrors($response, $service);

         return $response->adapt();
    }
}
