<?php


namespace Generali\Console\Commands;

use Generali\Assistance\Connection\GeneraliConnection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\SaleService;

class GeneraliSentinel extends Command
{
    protected $signature   = 'generali:sentinel';
    protected $description = 'Search status sale of Generali and Sync';
    protected $generaliConnection;
    protected $saleService;

    protected const STATUS_TRANSLATION = [
        ServiceStatus::PENDING_SUBMISSION => ServiceStatus::ACCEPTED,
        ServiceStatus::SUBMITTED          => ServiceStatus::ACCEPTED,
        ServiceStatus::APPROVED           => ServiceStatus::APPROVED,
        ServiceStatus::REJECTED           => ServiceStatus::REJECTED,
        ServiceStatus::CANCELED           => ServiceStatus::CANCELED,
    ];

    public function __construct(GeneraliConnection $generaliConnection, SaleService $saleService)
    {
        parent::__construct();
        $this->generaliConnection = $generaliConnection;
        $this->saleService        = $saleService;
    }

    public function handle(): void
    {
        $sales = $this->saleService->getSubmittedSalesToSentinel(Operations::GENERALI);

        foreach ($sales as $sale) {
            $this->iterateServicesAndUpdate($sale);
        }
    }

    public function iterateServicesAndUpdate(Sale $sale): void
    {
        $services = $sale->services->where('operator', '=', Operations::GENERALI);

        foreach ($services as $service) {
            try {
                $transaction = $this->generaliConnection->getTransactionByReference($service->serviceTransaction);
                $attributes  = $this->adaptServiceToUpdate($transaction);

                $this->saleService->updateService($service, $attributes);

                $this->addLog($transaction, $service);
            } catch (\Exception $exception) {
                Log::info('sale-not-found', [
                    'serviceTransaction' => $service->serviceTransaction,
                    'exception'          => $exception->getMessage()
                ]);
            }
        }
    }

    private function addLog(array $transaction, Service $service): void
    {
        $logs = data_get($transaction, 'logs');

        if (filled($logs)) {
            $this->saleService->pushLogService($service, [$logs]);
        }
    }

    public function adaptServiceToUpdate($transaction): array
    {
        return [
            'status'  => self::STATUS_TRANSLATION[data_get($transaction, 'data.service.status')],
            'premium' => data_get($transaction, 'service.license.premium')
        ];
    }
}
