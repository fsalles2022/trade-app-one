<?php

declare(strict_types=1);

namespace Tradehub\Services;

use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;

interface TradeHubSaleUpdate
{
    /**
     * Return service attributes to be updated by implemented strategy
     *
     * @param mixed[] $tradeHubRequestPayload
     * @return mixed[]
     */
    public function getServiceAttributesToUpdate(array $tradeHubRequestPayload, Sale $sale, Service $service): array;
}
