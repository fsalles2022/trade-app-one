<?php

namespace TradeAppOne\Domain\Services;

use TradeAppOne\Domain\Models\Collections\Service;

interface ContestBehavior
{
    public function contestService(Service $service, array $payload = []);
}
