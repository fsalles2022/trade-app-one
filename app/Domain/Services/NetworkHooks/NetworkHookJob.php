<?php

namespace TradeAppOne\Domain\Services\NetworkHooks;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use TradeAppOne\Domain\Models\Collections\Service;

class NetworkHookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;

    public $queue = 'NETWORK_HOOK';
    public $tries = 3;
    protected $hook;
    protected $service;

    public function __construct(string $hook, Service $service)
    {
        $this->hook    = $hook;
        $this->service = $service;
    }

    public function handle(): void
    {
        resolve($this->hook)->execute($this->service);
    }
}
