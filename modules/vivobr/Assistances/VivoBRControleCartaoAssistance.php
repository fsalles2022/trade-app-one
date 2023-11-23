<?php

namespace VivoBR\Assistances;

use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\AssistanceBehavior;

class VivoBRControleCartaoAssistance extends VivoBrAssistance implements AssistanceBehavior
{
    public function integrateService(Service $service, array $payload = [])
    {
        if ($service->status === ServiceStatus::SUBMITTED) {
            $lastsResponse = $service->log;
            $last['data']  = reset($lastsResponse);
            return $last;
        }

        $response = $this->activate($service);

        $response->isSuccess()
            ? $this->updateWithSuccess($response, $service, ServiceStatus::SUBMITTED)
            : $this->updateWithErrors($response, $service);

        return $response->adapt();
    }
}
