<?php

namespace TradeAppOne\Domain\Services\NetworkHooks;

use TradeAppOne\Domain\Models\Collections\Service;

interface NetworkHook
{
    public function execute(Service $service, array $options = []);
}
