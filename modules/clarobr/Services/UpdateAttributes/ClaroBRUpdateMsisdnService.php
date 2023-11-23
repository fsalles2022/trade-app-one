<?php
declare(strict_types=1);

namespace ClaroBR\Services\UpdateAttributes;

use Carbon\Carbon;
use ClaroBR\Connection\SivConnection;
use ClaroBR\Console\Commands\ClaroBRUpdateMsisdnCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHooksFactory;

class ClaroBRUpdateMsisdnService implements ClaroBRUpdateAttributes
{
    private $saleRepository;
    private $sivConnection;

    private const LIMIT_OF_UPDATE = 250;

    public function __construct(SivConnection $sivConnection, SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
        $this->sivConnection  = $sivConnection;
    }

    public function update(array $options): ?Collection
    {
        $initialDate = data_get($options, 'initial');
        $sales       = $this->getSalesToUpdated($initialDate);

        $mode = data_get($options, 'mode');
        if ($mode === ClaroBRUpdateMsisdnCommand::MODE_ALL) {
            return $this->updateAllMsisdn($sales);
        }
    }

    private function updateAllMsisdn(Collection $sales): Collection
    {
        $servicesUpdated = collect([]);

        $sales->each(function (Sale $sale) use ($servicesUpdated) {
            $sale->services->each(function (Service $service) use ($servicesUpdated) {

                Log::info('ClaroBRUpdateMsisdnService Processamento:', [
                    'venda_id' => data_get($service, 'operatorIdentifiers.venda_id', 0),
                    'serviceTransaction' => $service->serviceTransaction
                ]);

                $serviceInSiv = $this->sivConnection
                    ->querySales(['id' => data_get($service, 'operatorIdentifiers.venda_id', 0)])
                    ->toArray();

                $msisdn = data_get($serviceInSiv, 'data.data.0.services.0.numero_acesso');

                $sanitizedMsisdn = '';
                if ($msisdn) {
                    $sanitizedMsisdn = MsisdnHelper::removeCountryCode(MsisdnHelper::BR, $msisdn);
                    $servicesUpdated->push($service);
                }

                $this->saleRepository->updateService($service, ['msisdn' => $sanitizedMsisdn]);

                NetworkHooksFactory::run($service);
            });
        });

        return $servicesUpdated;
    }

    private function getSalesToUpdated(?string $initialDate): ?Collection
    {
        if ($initialDate !== null) {
            $initialDateFormatted = Carbon::createFromFormat('d/m/Y', $initialDate);
            return $this->saleRepository
                ->where('services.status', '=', ServiceStatus::APPROVED)
                ->where('services.operator', '=', Operations::CLARO)
                ->whereIn('services.operation', [
                    Operations::CLARO_CONTROLE_BOLETO,
                    Operations::CLARO_POS,
                    Operations::CLARO_BANDA_LARGA
                ])
                ->where('services.msisdn', 'exists', false)
                ->where('createdAt', '>=', $initialDateFormatted->startOfDay())
                ->limit(self::LIMIT_OF_UPDATE)
                ->get();
        }

        return $this->saleRepository
            ->where('services.status', '=', ServiceStatus::APPROVED)
            ->where('services.operator', '=', Operations::CLARO)
            ->whereIn('services.operation', [
                Operations::CLARO_CONTROLE_BOLETO,
                Operations::CLARO_POS,
                Operations::CLARO_BANDA_LARGA
            ])
            ->where('services.msisdn', 'exists', false)
            ->limit(self::LIMIT_OF_UPDATE)
            ->get();
    }
}
