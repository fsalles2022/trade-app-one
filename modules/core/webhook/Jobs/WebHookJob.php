<?php

namespace Core\WebHook\Jobs;

use Core\WebHook\Connections\WebHookConnection;
use Core\WebHook\Connections\WebHookFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;

class WebHookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue;

    public $queue = 'SALES_WEBHOOK';
    public $tries = 3;

    protected $changes;
    protected $serviceTransaction;

    public function __construct(string $serviceTransaction, array $changes)
    {
        $this->serviceTransaction = $serviceTransaction;
        $this->changes            = $changes;
    }

    public function handle(): void
    {
        //TODO Refatorar para remover integracao nao utilizada.
//        $service    = $this->findService($this->serviceTransaction);
//        $connection = WebHookFactory::make($service);
//
//        if ($connection instanceof WebHookConnection) {
//            $connection->push($service, $this->changes);
//        }
    }

    private function findService(string $serviceTransaction): Service
    {
        $service = $this->saleService()->findService($serviceTransaction);
        throw_if($service === null, ServiceExceptions::notFound());

        return $service;
    }

    private function saleService(): SaleService
    {
        return resolve(SaleService::class);
    }
}
