<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\DeviceRepository;
use TradeAppOne\Facades\UserPolicies;

class DeviceService
{
    protected $deviceRepository;
    protected $hierarchyService;

    public function __construct(DeviceRepository $deviceRepository, HierarchyService $hierarchy)
    {
        $this->deviceRepository = $deviceRepository;
        $this->hierarchyService = $hierarchy;
    }

    public function getDevices(): Collection
    {
        return $this->deviceRepository->all();
    }

    public function getTypes(): array
    {
        $allDevices = $this->deviceRepository->all()->first()->get()->sortBy('model');
        $allBrands  = $allDevices->whereNotIn('brand', [ null, ''])->groupBy('brand')
            ->map(static function (Collection  $device, $key) {
                return  [
                    'id'     => $key,
                    'label'  => ucwords(mb_strtolower($key)),
                    'types' => $device->pluck('type')->unique()->values()->toArray()
                ];
            })->sortBy('id');

        $types = $this->deviceRepository->allTypes()->map(function ($type) {
            return [
                'id'  => $type,
                'label' => ucwords(mb_strtolower($type))
            ];
        })->sortBy('id');

        return [
            'types' => $types->values(),
            'brands' => $allBrands->values(),
            'models' => $allDevices->values()
        ];
    }

    public function getDevicesFilteredByType($filters): Collection
    {
        return DeviceRepository::filterByType($filters['type']);
    }

    public function getDevicesOutsourcedByNetwork(User $user): Collection
    {
        $networksThatBelongsToUser = UserPolicies::setUser($user)->getNetworksAuthorized();
        $networksIds               = $networksThatBelongsToUser->pluck('id')->toArray();
        return $this->deviceRepository->devicesOutsourcedByNetwork($networksIds);
    }

    public function devicesPaginated($filters): LengthAwarePaginator
    {
        $devices = $this->deviceRepository->filter($filters);
        return $this->deviceRepository->paginate($devices, 10);
    }
}
