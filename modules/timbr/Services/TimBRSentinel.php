<?php

namespace TimBR\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use TimBR\Connection\TimBRConnection;
use TimBR\Enumerators\TimBRStatus;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\SaleService;

class TimBRSentinel
{
    /** @var TimBRConnection $connection */
    protected $connection;

    /** @var SaleService $saleService*/
    protected $saleService;

    /** @var array[] */
    protected $servicesUpdated = [];

    protected $explainedMode = false;

    protected const MAX_SEARCH_TRIES = 25;
    protected const MIN_SEARCH_TRIES = 0;

    public function __construct(TimBRConnection $connection, SaleService $saleService)
    {
        $this->connection  = $connection;
        $this->saleService = $saleService;
    }

    public function setExplainedMode(bool $mode): void
    {
        $this->explainedMode = $mode;
    }

    public function sentinelDailySalesByProtocol(): array
    {
        if ($this->explainedMode) {
            dump('--------- Tim Sentinel: Daily Mode ---------');
            dump('## Running Current Day');
            dump('--------------------------------------------');
        }

        return $this->sentinelSalesByProtocol([
            'initial-date' => now()->startOfDay()->format('Y-m-d-H-i'),
            'final-date' => now()->endOfDay()->format('Y-m-d-H-i'),
            'min-tries' => self::MIN_SEARCH_TRIES,
            'max-tries' => 5
        ]);
    }

    public function sentinelYearlySalesByProtocol(): array
    {
        $listOfUpdated  = [];
        $currentDay     = now()->startOfDay();
        $firstDayOfYear = now()->startOfYear()->startOfDay();

        while ($currentDay->diffInDays($firstDayOfYear) > 0) {
            $options['initial-date'] = $currentDay->format('Y-m-d-H-i');
            $options['final-date']   = $currentDay->endOfDay()->format('Y-m-d-H-i');
            $options['min-tries']    = 6;
            $options['max-tries']    = self::MAX_SEARCH_TRIES;

            if ($this->explainedMode) {
                dump('--------- Tim Sentinel: Yearly Mode ---------');
                dump('## Running ' . $options['initial-date'] . ' - ' . $options['final-date']);
                dump('---------------------------------------------');
            }

            $listOfUpdated[] = $this->sentinelSalesByProtocol($options);
            $currentDay->subDays(1)->startOfDay();
            $this->clearServicesUpdated();
        }
        return $listOfUpdated;
    }

    public function sentinelGetAllSalesByProtocol(): array
    {
        $listOfUpdated     = [];
        $currentDay        = now()->startOfDay();
        $startDayFirstYear = now()->subYears(4)->startOfDay();

        while ($currentDay->diffInDays($startDayFirstYear) > 0) {
            $options['initial-date'] = $currentDay->format('Y-m-d-H-i');
            $options['final-date']   = $currentDay->endOfDay()->format('Y-m-d-H-i');
            $options['min-tries']    = self::MIN_SEARCH_TRIES;
            $options['max-tries']    = self::MAX_SEARCH_TRIES;

            if ($this->explainedMode) {
                dump('--------- Tim Sentinel: Fully Mode ---------');
                dump('## Running ' . $options['initial-date'] . ' - ' . $options['final-date']);
                dump('--------------------------------------------');
            }

            $listOfUpdated[] = $this->sentinelSalesByProtocol($options);
            $currentDay->subDays(1)->startOfDay();
            $this->clearServicesUpdated();
        }
        return $listOfUpdated;
    }

    /** @param mixed[] $statusFromTim */
    private function updateStatus(array $statusFromTim, Service $service): void
    {
        $toUpdate = self::translateStatus($statusFromTim);
        if (filled($toUpdate)) {
            $this->saleService->updateService($service, $toUpdate);
            $updated                 = array_merge([
                'serviceTransaction' => $service->serviceTransaction,
                'timProtocolSearchTries' => data_get($service, 'timProtocolSearchTries')
            ], $toUpdate);
            $this->servicesUpdated[] = $updated;
        }
    }

    private static function translateStatus(array $serviceFromTim): array
    {
        $toUpdate = [];
        if (in_array(data_get($serviceFromTim, 'status', ''), TimBRStatus::APPROVED, true)) {
            $toUpdate['statusThirdParty'] = data_get($serviceFromTim, 'status', '');
            $toUpdate['status']           = ServiceStatus::APPROVED;
        } elseif (in_array(data_get($serviceFromTim, 'status', ''), TimBRStatus::CANCELED, true)) {
            $toUpdate['statusThirdParty'] = data_get($serviceFromTim, 'status', '');
            $toUpdate['status']           = ServiceStatus::CANCELED;
        } elseif (in_array(data_get($serviceFromTim, 'status', ''), TimBRStatus::REJECTED, true)) {
            $toUpdate['statusThirdParty'] = data_get($serviceFromTim, 'status', '');
            $toUpdate['status']           = ServiceStatus::REJECTED;
        } elseif (in_array(data_get($serviceFromTim, 'status', ''), TimBRStatus::ACCEPTED, true)) {
            $toUpdate['statusThirdParty'] = data_get($serviceFromTim, 'status', '');
            $toUpdate['status']           = ServiceStatus::ACCEPTED;
        }
        return $toUpdate;
    }

    private function sentinelSalesByProtocol(array $options): array
    {
        $initialDateArgument = data_get($options, 'initial-date');
        $finalDateArgument   = data_get($options, 'final-date');

        $minSearchTries = data_get($options, 'min-tries');
        $maxSearchTries = data_get($options, 'max-tries');

        if ($initialDateArgument !== null) {
            $options['initialDate'] = Carbon::createFromFormat('Y-m-d-H-i', $initialDateArgument);
        }

        if ($finalDateArgument !== null) {
            $options['finalDate'] = Carbon::createFromFormat('Y-m-d-H-i', $finalDateArgument);
        }

        $salesFromAllNetworks = $this->saleService->getSubmittedSalesToSentinel(Operations::TIM, $options)
            ->groupBy(static function ($sale) {
                return $sale->pointOfSale['network']['slug'];
            });

        $salesFromAllNetworks->each(function ($salesFromNetwork, $network) use ($minSearchTries, $maxSearchTries) {
            foreach ($salesFromNetwork as $sale) {
                $services = $sale->services
                    ->where('status', ServiceStatus::ACCEPTED);

                if ($minSearchTries !== null && $maxSearchTries !== null) {
                    $services = $services->filter(function (Service $service) use ($minSearchTries, $maxSearchTries): bool {
                        $timProtocolSearchTries = data_get($service, 'timProtocolSearchTries', 0);
                        if ($timProtocolSearchTries === 0 && $minSearchTries === 0) {
                            return true;
                        }
                        return $timProtocolSearchTries >= $minSearchTries && $timProtocolSearchTries <= $maxSearchTries;
                    });
                }
                $this->compareWithProtocol($network, $services);
            }
        });
        return $this->servicesUpdated;
    }

    private function compareWithProtocol($network, Collection $services): void
    {
        foreach ($services as $service) {
            try {
                $protocol = data_get($service, 'operatorIdentifiers.protocol');
                if ($protocol) {
                    $statusFromTim = $this->getServiceByProtocol($network, $protocol);
                    if (filled($statusFromTim)) {
                        if ($this->explainedMode) {
                            dump($service->serviceTransaction . ' - ' . data_get($statusFromTim, 'status', ''));
                        }

                        $this->updateStatus($statusFromTim, $service);
                    }
                }
            } catch (\Throwable $exception) {
                if ($this->explainedMode) {
                    dump($service->serviceTransaction . ' - ' . $exception->getMessage());
                }

                Log::info('TimBRSentinel-Protocol', [
                    'error' => $exception->getMessage(),
                    'code' => $exception->getCode()
                ]);
            } finally {
                $this->increaseNumberOfTries($service);
            }
        }
    }

    private function getServiceByProtocol(string $network, $protocol): array
    {
        return $this->connection
            ->selectCustomConnection($network)
            ->getOrderStatusByProtocol($protocol)
            ->toArray();
    }

    private function increaseNumberOfTries(Service $service): void
    {
        $tries                              = (int) data_get($service, 'timProtocolSearchTries', 0);
        $toUpdate['timProtocolSearchTries'] = ++$tries;

        if ($tries >= self::MAX_SEARCH_TRIES) {
            $serviceFounded     = $this->saleService->findService($service->serviceTransaction);
            $toUpdate['status'] = $serviceFounded->status === ServiceStatus::ACCEPTED
                ? ServiceStatus::CANCELED
                : $serviceFounded->status;
        }

        $this->saleService->updateService($service, $toUpdate);

        $this->setServiceUpdated($service, $toUpdate);
    }

    /** @param mixed[] $data */
    private function setServiceUpdated(Service $service, array $data): void
    {
        if (key_exists($service->serviceTransaction, $this->servicesUpdated) === true) {
            $data = array_merge(
                $this->servicesUpdated[$service->serviceTransaction],
                $data
            );
        }
        $this->servicesUpdated[$service->serviceTransaction] = $data;
    }

    public function clearServicesUpdated():void
    {
        $this->servicesUpdated = [];
    }
}
