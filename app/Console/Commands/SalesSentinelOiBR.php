<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use OiBR\Assistance\OiControleCartaoAssistance;
use OiBR\Connection\OiBRConnection;
use OiBR\Enumerators\OiBRStatus;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Logging\LogEnumerators;
use TradeAppOne\Domain\Services\SaleService;

class SalesSentinelOiBR extends Command
{
    const STATUS           = 'statusTransacao';
    protected $signature   = 'sentinel:oi';
    protected $description = 'Search sales in Oi and sync status';
    protected $connection;
    protected $saleService;

    public function __construct(OiBRConnection $connection, SaleService $saleService)
    {
        parent::__construct();
        $this->connection  = $connection;
        $this->saleService = $saleService;
    }

    public function handle()
    {
        $salesUpdated = [];
        $sales        = $this->saleService->getSubmittedSalesToSentinel(Operations::OI);
        foreach ($sales as $sale) {
            $services = $sale->services()
                ->whereIn('status', [ServiceStatus::SUBMITTED, ServiceStatus::ACCEPTED])
                ->where('operator', Operations::OI);
            foreach ($services as $service) {
                $identifier = $service->msisdn;
                $operation  = $service->operation;
                try {
                    if ($operation === Operations::OI_CONTROLE_BOLETO) {
                        $serviceFromOi = $this->getActualServiceStatus($identifier)->first();
                        if (filled($serviceFromOi)) {
                            $toUpdate = $this->translateStatus($serviceFromOi);
                            $this->saleService->updateService($service, $toUpdate);
                            array_push($salesUpdated, [
                                $service->serviceTransaction => json_encode($toUpdate, JSON_PRETTY_PRINT)
                            ]);
                        }
                    } else {
                        $assistance = resolve(OiControleCartaoAssistance::class);
                        $assistance->searchOiCartaoStatus($service);
                    }
                } catch (\Exception $exception) {
                    integrationLogger(LogEnumerators::OI_SENTINEL_FAILED)
                        ->tags(LogEnumerators::OI_SENTINEL_TAGS)
                        ->extra($salesUpdated);
                }
            }
        }
    }

    public function getActualServiceStatus(string $identifier): Collection
    {
        $responseWithSales = $this->connection->controleBoletoQuery($identifier)->toArray();
        if (empty($responseWithSales)) {
            return new Collection();
        }
        return collect([$responseWithSales]);
    }

    public function translateStatus(array $serviceFromSun): array
    {
        $toUpdate = [];
        if (in_array($serviceFromSun[self::STATUS], OiBRStatus::APPROVED)) {
            $toUpdate['statusThirdParty'] = $serviceFromSun[self::STATUS];
            $toUpdate['status']           = ServiceStatus::APPROVED;
        }
        if (in_array($serviceFromSun[self::STATUS], OiBRStatus::CANCELED)) {
            $toUpdate['statusThirdParty'] = $serviceFromSun[self::STATUS];
            $toUpdate['status']           = ServiceStatus::CANCELED;
        }
        if (in_array($serviceFromSun[self::STATUS], OiBRStatus::REJECTED)) {
            $toUpdate['statusThirdParty'] = $serviceFromSun[self::STATUS];
            $toUpdate['status']           = ServiceStatus::REJECTED;
        }
        if (in_array($serviceFromSun[self::STATUS], OiBRStatus::ACCEPTED)) {
            $toUpdate['statusThirdParty'] = $serviceFromSun[self::STATUS];
            $toUpdate['status']           = ServiceStatus::ACCEPTED;
        }

        return filled($toUpdate) ? $toUpdate : [];
    }
}
