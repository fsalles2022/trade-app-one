<?php

namespace TradeAppOne\Domain\Logging\Heimdall;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\User;

class HeimdallUserMapper
{
    public static function map(?User $user)
    {
        if ($user) {
            $pointsOfSale       = $user->pointsOfSale;
            $mappedPointsOfSale = self::mapPointsOfSale($pointsOfSale);
            return [
                'id'          => data_get($user, 'id'),
                'cpf'         => data_get($user, 'cpf'),
                'role_id'     => data_get($user, 'role.id'),
                'role_slug'   => data_get($user, 'role.slug'),
                'name'        => data_get($user, 'firstName') . ' ' . data_get($user, 'lastName'),
                'pointOfSale' => $mappedPointsOfSale
            ];
        }
        return [
            'cpf' => 'APP_ROUTINE'
        ];
    }

    protected static function mapPointsOfSale(Collection $pointsOfSale)
    {
        $collectionMapped = [];
        foreach ($pointsOfSale as $pointOfSale) {
            $mapped = [
                'networkId'           => data_get($pointOfSale, 'network.id'),
                'network'             => data_get($pointOfSale, 'network.slug'),
                'id'                  => data_get($pointOfSale, 'id'),
                'slug'                => data_get($pointOfSale, 'slug'),
                'areaCode'            => data_get($pointOfSale, 'areaCode'),
                'state'               => 'BR-' . data_get($pointOfSale, 'state'),
                'cep'                 => data_get($pointOfSale, 'cep'),
                'providerIdentifiers' => data_get($pointOfSale, 'providerIdentifiers'),
            ];
            array_push($collectionMapped, $mapped);
        }

        return $collectionMapped;
    }
}
