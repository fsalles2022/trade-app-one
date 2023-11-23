<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Models\Tables\UserPendingRegistration;
use TradeAppOne\Domain\Repositories\Tables\UserThirdPartyRegistrationRepository;

class UserThirdPartyRegistrationService extends BaseService
{
    protected $repository;

    public function __construct(UserThirdPartyRegistrationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getSalesmen(): Collection
    {
        return $this->userService->getSalesmen();
    }

    public function getSalesmenUnregisteredFrom(string $operator): ?Collection
    {
        return $this->repository->where('operator', '=', $operator)->where('done', false)->get();
    }

    public function getPendingRegistrationOf(User $user, string $operator)
    {
        return $this->repository->where('operator', '=', $operator)->where('userId', $user->id)->get();
    }

    public function getAll(): ?Collection
    {
        return $this->repository->all();
    }

    public function addToTable(User $user, PointOfSale $actual): bool
    {
        $network           = $actual->network;
        $availableServices = $network->services;
        if ($this->roleService->roleMakeSales($user->role)) {
            foreach ($availableServices as $service) {
                $this->create($user, $actual, $service['operator']);
            }
        }

        return false;
    }

    public function create(
        User $user,
        PointOfSale $pointOfSale,
        string $operator,
        string $log = ''
    ): UserPendingRegistration {
        $pending = $this->repository
            ->where('userId', $user->id)
            ->where('operator', $operator)
            ->first();
        if ($pending) {
            $this->repository->delete($pending);
        }
        return $this->repository->create([
            'userId'        => $user->id,
            'pointOfSaleId' => $pointOfSale->id,
            'operator'      => $operator,
            'log'           => $log
        ]);
    }

    public function update(UserPendingRegistration $instance, array $attributes)
    {
        $this->repository->update($instance, $attributes);
    }

    public function flag(UserPendingRegistration $instance, bool $result, string $log = '')
    {
        return $this->repository->update($instance, ['done' => $result, 'log' => $log]);
    }
}
