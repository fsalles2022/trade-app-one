<?php

namespace TradeAppOne\Domain\Services;

use TradeAppOne\Domain\Models\Collections\Service;

interface AssistanceBehavior
{
    public function integrateService(Service $service, array $payload = []);
}
