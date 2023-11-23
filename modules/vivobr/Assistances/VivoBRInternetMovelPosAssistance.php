<?php

namespace VivoBR\Assistances;

use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\AssistanceBehavior;

class VivoBRInternetMovelPosAssistance extends VivoBrAssistance implements AssistanceBehavior
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
