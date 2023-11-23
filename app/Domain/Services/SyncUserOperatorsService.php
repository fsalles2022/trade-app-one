<?php

namespace TradeAppOne\Domain\Services;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Facades\UserPolicies;
use VivoBR\Jobs\VivoSyncUsersJob;

class SyncUserOperatorsService
{
    const SYNCS_AVAILABLE = [
        Operations::VIVO
    ];

    public function sync(User $user, PointOfSale $pointOfSale, array $changes = [])
    {
        if ($changes && empty(data_get($changes, 'attached'))) {
            return;
        }

        $syncs = $this->operatorsToSync($user);

        foreach ($syncs as $sync) {
            $this->{$sync}($user, $pointOfSale);
        }
    }

    public function vivo(User $user, PointOfSale $pointOfSale)
    {
        dispatch(new VivoSyncUsersJob($user, $pointOfSale));
    }

    private function operatorsToSync($user): array
    {
        $operators = UserPolicies::setUser($user)
            ->getOperatorsHasAuthorized();

        return array_intersect(self::SYNCS_AVAILABLE, $operators);
    }
}
