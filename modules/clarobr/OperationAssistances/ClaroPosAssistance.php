<?php

namespace ClaroBR\OperationAssistances;

use TradeAppOne\Domain\Models\Collections\Service;

class ClaroPosAssistance implements ClaroAssistantBehavior
{
    use ClaroActivationAssistance;

    public function activate(Service $service, array $payload = [])
    {
        return $this->activation($service, $payload);
    }
}
