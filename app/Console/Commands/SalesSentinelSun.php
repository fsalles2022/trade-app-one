<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Services\SaleService;
use VivoBR\Connection\SunConnection;
use VivoBR\Enumerators\SunStatus;

// TODO - INTEGRACAO DESATIVADA.

class SalesSentinelSun extends Command
{
    protected $signature = 'sentinel:sun';
    protected $connection;
    protected $saleService;

    protected $description = 'Command description';

    public function __construct(SunConnection $sunConnection, SaleService $saleService)
    {
        $this->connection  = $sunConnection;
        $this->saleService = $saleService;
        parent::__construct();
    }

    public function handle()
    {
        $salesUpdated = [];
        $sales        = $this->saleService->getSubmittedSalesToSentinel(Operations::VIVO);
        foreach ($sales as $sale) {
            $services = $sale->services()
                ->whereIn('status', [ServiceStatus::SUBMITTED, ServiceStatus::ACCEPTED])
                ->where('operator', Operations::VIVO);
            foreach ($services as $service) {
                $identifiers = $service->operatorIdentifiers;
                try {
                    $network        = $sale->pointOfSale['network']['slug'];
                    $saleOnSun      = $this->getActualServiceStatus($network, $identifiers);
                    $serviceFromSun = $saleOnSun[0]
                        ->where('id', $identifiers['idServico'])
                        ->first();
                    if (filled($serviceFromSun)) {
                        $toUpdate = $this->translateStatus($serviceFromSun, $saleOnSun[1]);
                        $toUpdate = $this->translateObservations($toUpdate, $saleOnSun[1]);
                        $this->saleService->updateService($service, $toUpdate);
                        array_push($salesUpdated, [
                            $service->serviceTransaction => json_encode($toUpdate, JSON_PRETTY_PRINT)
                        ]);
                    }
                } catch (\Exception $exception) {
                    Log::info('sale-not-found-sun', [
                        'serviceTransaction' => $service->serviceTransaction,
                        'exception' => $exception->getMessage()
                    ]);
                }
            }
        }
    }

    public function getActualServiceStatus(string $network, array $identifiers): array
    {
        try {
            $responseWithSale = $this->connection
                ->selectCustomConnection($network)
                ->querySales(['id' => $identifiers['idVenda']])
                ->toArray();
            return [collect($responseWithSale['vendas'][0]['servicos']), $responseWithSale['vendas'][0]];
        } catch (\Exception $exception) {
            return [new Collection(), []];
        }
    }

    public function translateStatus(array $serviceFromSun, $venda): array
    {
        $statusVenda                  = data_get($venda, 'status') . ' - ';
        $toUpdate['msisdn']           = (string) data_get($serviceFromSun, 'numeroAcesso');
        $toUpdate['statusThirdParty'] = $statusVenda . $serviceFromSun['status'];
        if (in_array($serviceFromSun['status'], SunStatus::APPROVED, true)) {
            $toUpdate['status'] = ServiceStatus::APPROVED;
        }
        if (in_array($serviceFromSun['status'], SunStatus::CANCELED, true)) {
            $toUpdate['status'] = ServiceStatus::CANCELED;
        }
        if (in_array($serviceFromSun['status'], SunStatus::REJECTED, true)) {
            $toUpdate['status'] = ServiceStatus::REJECTED;
        }
        if (in_array($serviceFromSun['status'], SunStatus::ACCEPTED, true)) {
            $toUpdate['status'] = ServiceStatus::ACCEPTED;
        }

        return filled($toUpdate) ? array_filter($toUpdate) : [];
    }

    public function translateObservations(array $toUpdate, $venda): array
    {
        $observations             = isset($venda['observacoes']) ? collect($venda['observacoes']) : collect();
        $toUpdate['observations'] = $observations->map(static function (array $obs) {
            return array_filter([
                'id' => data_get($obs, 'id'),
                'reason' => data_get($obs, 'motivo'),
                'source' => data_get($obs, 'origem'),
                'dateTime' => data_get($obs, 'dataHora'),
                'observation' => data_get($obs, 'observacao'),
            ]);
        })->toArray();
        return filled($toUpdate) ? array_filter($toUpdate) : [];
    }
}
