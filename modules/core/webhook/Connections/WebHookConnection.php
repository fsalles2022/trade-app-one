<?php

namespace Core\WebHook\Connections;

use TradeAppOne\Domain\Models\Collections\Service;

interface WebHookConnection
{
    public function push(Service $service, array $changes);

    public function isForMe(Service $service): bool;
}
