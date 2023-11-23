<?php

namespace Buyback\Repositories;

use Buyback\Services\Waybill;
use Illuminate\Support\Collection;
use MongoDB\BSON\UTCDateTime;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;
use TradeAppOne\Facades\Uniqid;

class WaybillRepository
{
    private $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    /** @param string[] $operations */
    public function findServicesWithWaybillAvailable(Collection $pointsOfSale, array $operations, bool $withDrawn = false): Collection
    {
        $cnpjs = $pointsOfSale->pluck('cnpj')->toArray();

        $query = $this->saleRepository
            ->createModel()
            ->newQuery()
            ->whereIn('pointOfSale.cnpj', $cnpjs)
            ->whereIn('services.operation', $operations)
            ->where('services.status', ServiceStatus::ACCEPTED)
            ->whereNull('services.waybill.printedAt');

        if ($withDrawn === true) {
            $query->where('services.waybill.withdrawn', true);
        }

        return $query->get();
    }

    /**
     * @param User $user
     * @param Waybill $waybill
     * @return Waybill
     */
    public function persistWaybill(User $user, Waybill $waybill): Waybill
    {
        $waybillId = Uniqid::generate();
        $mongoDate = new UTCDateTime($waybill->date);

        $servicesUpdated = new Collection();
        foreach ($waybill->services as $service) {
            $currentWaybill = $service->waybill ?? [];

            $serviceAlreadyUpdated = $this->saleRepository->updateService($service, [
                'waybill' => array_merge($currentWaybill, [
                    'printedAt' => $mongoDate,
                    'id'        => $waybillId,
                    'auditor' => [
                        'firstName' => $user->firstName,
                        'lastName' => $user->lastName
                    ],
                ])
            ]);

            $servicesUpdated->push($serviceAlreadyUpdated);
        }

        $waybill->id       = $waybillId;
        $waybill->services = $servicesUpdated;

        return $waybill;
    }

    /**
     * @param User $user
     * @param string $serviceTransaction
     * @return Service|null
     * @throws SaleNotFoundException
     */
    public function checkWaybillDevice(User $user, string $serviceTransaction): ?Service
    {
        $service = $this->saleRepository->findInSale($serviceTransaction);
        return $this->saleRepository->updateService($service, [
            'waybill' => [
                'withdrawnDate' => now()->toIso8601String(),
                'withdrawn'     => true,
                'auditor' => [
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName
                ],
            ]
        ]);
    }
}
