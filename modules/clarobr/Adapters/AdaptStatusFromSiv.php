<?php

namespace ClaroBR\Adapters;

use ClaroBR\Enumerators\SivStatus;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

class AdaptStatusFromSiv
{
    public static function adapt(string $status): ?string
    {
        return SivStatus::ORIGINAL_STATUS[$status] ?? null;
    }

    public static function setDataToUpdate(array $serviceFromSiv, array $identifiers): array
    {
        $toUpdate = [];
        if ($acceptance = data_get($serviceFromSiv, 'aceite_voz')) {
            $identifiers['acceptance']       = $acceptance;
            $toUpdate['operatorIdentifiers'] = $identifiers;
        }
        if (in_array($serviceFromSiv['status'], SivStatus::APPROVED)) {
            $toUpdate['statusThirdParty'] = $serviceFromSiv['status'];
            $toUpdate['status']           = ServiceStatus::APPROVED;
        }
        if (in_array($serviceFromSiv['status'], SivStatus::CANCELED)) {
            $toUpdate['statusThirdParty'] = $serviceFromSiv['status'];
            $toUpdate['status']           = ServiceStatus::CANCELED;
        }
        if (in_array($serviceFromSiv['status'], SivStatus::REJECTED)) {
            $toUpdate['statusThirdParty'] = $serviceFromSiv['status'];
            $toUpdate['status']           = ServiceStatus::REJECTED;
        }
        if (in_array($serviceFromSiv['status'], SivStatus::ACCEPTED)) {
            $toUpdate['statusThirdParty'] = $serviceFromSiv['status'];
            $toUpdate['status']           = ServiceStatus::ACCEPTED;
        }

        return filled($toUpdate) ? $toUpdate : [];
    }
}
