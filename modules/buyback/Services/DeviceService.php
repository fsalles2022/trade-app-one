<?php

namespace Buyback\Services;

use Buyback\Repositories\DeviceRepository;
use Buyback\Repositories\DevicesNetworkRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DeviceService
{
    protected $deviceRepository;
    protected $deviceNetworkRepository;

    public function __construct(
        DeviceRepository $deviceRepository,
        DevicesNetworkRepository $devicesNetworkRepository
    ) {
        $this->deviceRepository        = $deviceRepository;
        $this->deviceNetworkRepository = $devicesNetworkRepository;
    }

    public function devicesByNetworksIdWithFilters(array $networksId, array $filters = []): ?Collection
    {
        return $this->deviceRepository->findAllByNetworksIdWithFilters($networksId, $filters);
    }

    public function deviceByDeviceIdAndNetworkId(int $deviceId, int $networkId) :?array
    {
        return $this->deviceRepository->findOneDeviceByDeviceIdAndNetworkId($deviceId, $networkId);
    }

    public function findDeviceById(int $deviceId)
    {
        return $this->deviceRepository->find($deviceId);
    }

    public function deviceTierNotes()
    {
        return $this->deviceRepository->findDeviceTierNotes();
    }

    public function deviceById(?int $deviceId)
    {
        return $this->deviceRepository->find($deviceId);
    }

    public function findDeviceByPartialSku(string $partialSku): Collection
    {
        $userLogged          = Auth::user();
        $networkToLoggedUser = data_get($userLogged->getNetwork(), 'slug', '');
        return $this->deviceNetworkRepository->findByNetworkSlugAndPartialSku(
            $networkToLoggedUser,
            $partialSku
        );
    }


    /**
     * @param string $sku
     * @return Collection
     */
    public function findDeviceBySku(string $sku): Collection
    {
        $userLogged          = Auth::user();
        $networkToLoggedUser = data_get($userLogged->getNetwork(), 'slug', '');
        return $this->deviceNetworkRepository->findByNetworkSlugAndCompleteSku(
            $networkToLoggedUser,
            $sku
        );
    }

    public function getAllIpads(): Builder
    {
        $userLogged          = Auth::user();
        $networkToLoggedUser = data_get($userLogged->getNetwork(), 'slug', '');
        return $this->deviceNetworkRepository->getAllIpads($networkToLoggedUser);
    }
}
