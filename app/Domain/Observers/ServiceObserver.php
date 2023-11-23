<?php


namespace TradeAppOne\Domain\Observers;

use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHooksFactory;

class ServiceObserver
{
    public function updated(Service $service): void
    {
        if (($service->getOriginal('status') !== $service->getAttribute('status'))) {
            NetworkHooksFactory::run($service);
        }
    }
}
