<?php

namespace TimBR\Assistance\TimBROperationsAssistances;

use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\Models\Collections\Service;

interface TimBROperationsAssistanceInterface
{
    public function activate(Service $service, array $payload = []): ResponseAdapterAbstract;
}
