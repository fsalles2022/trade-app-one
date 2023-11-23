<?php

namespace ClaroBR\OperationAssistances;

use TradeAppOne\Domain\Models\Collections\Service;

class ClaroControleBoletoAssistant implements ClaroAssistantBehavior
{
    use ClaroActivationAssistance;

    /** @throws */
    public function activate(Service $service, array $extraPayload = [])
    {
        return $this->activation($service, $extraPayload);
    }
}
