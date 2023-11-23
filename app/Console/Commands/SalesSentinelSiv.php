<?php

namespace TradeAppOne\Console\Commands;

use ClaroBR\Connection\SivConnection;
use ClaroBR\Enumerators\SivOperations;
use ClaroBR\Enumerators\SivStatus;
use ClaroBR\Exceptions\SivInvalidCredentialsException;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Domain\Components\Helpers\Period;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Services\SaleService;

class SalesSentinelSiv extends Command
{
    private const CARTAO_CREDITO_LIO = 'CARTAO_CREDITO_LIO';

    protected $signature = 'sentinel:siv  {--initial-date=} {--final-date=}';
    protected $connection;
    protected $description = 'Search sales in SIV and sync status';
    protected $saleService;

    public function __construct(SivConnection $sivConnection, SaleService $saleService)
    {
        parent::__construct();
        $this->connection  = $sivConnection;
        $this->saleService = $saleService;
    }

    public function handle(): void
    {
        $salesUpdated = [];
        $period       = Period::parseFromCommand($this->options());
        $sales        = $this->saleService->getSubmittedSalesToSentinel(Operations::CLARO, $period->toArray());
        foreach ($sales as $sale) {
            $services = $sale->services()->whereIn('status', [ServiceStatus::SUBMITTED, ServiceStatus::ACCEPTED]);
            foreach ($services as $service) {
                $identifiers = $service->operatorIdentifiers;
                if (data_get($service, 'operator') !== Operations::CLARO ||
                    $identifiers === null ||
                    ! is_array($identifiers)
                ) {
                    Log::info('sale-invalid-format-siv', [
                        'serviceTransaction' => data_get($service, 'serviceTransaction')
                    ]);
                    continue;
                }

                try {
                    $serviceFromSiv = $this->getActualServiceStatus($identifiers, $service->invoiceType)
                        ->where('id', $identifiers['servico_id'])->first();
                    if (filled($serviceFromSiv)) {
                        $toUpdate = $this->setDataToUpdate($serviceFromSiv, $identifiers);
                        $this->saleService->updateService($service, $toUpdate);
                        $salesUpdated[] = [
                            $service->serviceTransaction => json_encode($toUpdate, JSON_PRETTY_PRINT)
                        ];
                    }
                } catch (Exception $exception) {
                    Log::info('sale-not-found-siv', [
                        'serviceTransaction' => $service->serviceTransaction,
                        'exception' => $exception->getMessage()
                    ]);
                }
            }
        }
    }

    public function getActualServiceStatus(array $identifiers, string $invoiceType = null): Collection
    {
        $saleId    = data_get($identifiers, 'venda_id');
        $serviceId = data_get($identifiers, 'servico_id');

        try {
            if ($invoiceType === self::CARTAO_CREDITO_LIO) {
                $response = $this->connection->checkPayment($serviceId);

                return collect($response->toArray());
            }

            $responseWithSale = $this->connection->querySales(['id' => $saleId])->toArray();
            $salesFromSiv     = collect($responseWithSale['data']['data']);
            $sale             = $salesFromSiv->where('id', $saleId)->first();

            return collect($sale['services']);
        } catch (SivInvalidCredentialsException $exception) {
            return new Collection();
        }
    }

    public function setDataToUpdate(array $serviceFromSiv, array $identifiers): array
    {
        $toUpdate = [];
        if ($acceptance = data_get($serviceFromSiv, 'aceite_voz')) {
            $identifiers['acceptance']       = $acceptance;
            $toUpdate['operatorIdentifiers'] = $identifiers;
        }
        $toUpdate['statusThirdParty'] = $serviceFromSiv['status'];
        if (in_array($serviceFromSiv['status'], SivStatus::APPROVED, true)) {
            $toUpdate['status'] = ServiceStatus::APPROVED;
        }
        if (in_array($serviceFromSiv['status'], SivStatus::CANCELED, true)) {
            $toUpdate['status'] = ServiceStatus::CANCELED;
        }
        if (in_array($serviceFromSiv['status'], SivStatus::REJECTED, true)) {
            $toUpdate['status'] = ServiceStatus::REJECTED;
        }
        if (in_array($serviceFromSiv['status'], SivStatus::ACCEPTED, true)) {
            $toUpdate['status'] = ServiceStatus::ACCEPTED;
        }

        $mode = data_get($serviceFromSiv, 'tipo_servico');
        $operation = data_get($serviceFromSiv, 'plano_tipo');

        if ($operation === SivOperations::CONTROLE_FACIL && $mode === 'ATIVACAO') {
            $toUpdate['msisdn'] = data_get($serviceFromSiv, 'numero_acesso');
        }

        return filled($toUpdate) ? $toUpdate : [];
    }
}
