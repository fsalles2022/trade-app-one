<?php

namespace ClaroBR\OperationAssistances;

use TradeAppOne\Domain\Models\Collections\Service;

interface ClaroAssistantBehavior
{
    public function activate(Service $service, array $payload = []);
}
