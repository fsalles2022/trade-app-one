<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Facades\UserPolicies;

class NetworkRepository
{
    public const EMAIL     = 'EMAIL';
    private const OPERATOR = 'operator';

    public static function create(array $attributes): Network
    {
        return Network::create($attributes);
    }

    public static function findOneBy(string $key, $value): Builder
    {
        return Network::query()->where($key, '=', $value);
    }

    public static function getByFilter(array $parameters = []): Builder
    {
        $networkQuery = Network::query();
        $networksId   = UserPolicies::getNetworksAuthorized()->pluck('id');

        foreach ($parameters as $key => $value) {
            $key === self::OPERATOR
                ? $networkQuery = self::availableServiceRelation($parameters)
                : $networkQuery = $networkQuery->where($key, 'like', "%$value%");
        }

        return $networkQuery->whereIn('id', $networksId);
    }

    public static function availableServiceRelation(?array $parameters, string $column = null): Builder
    {
        return Network::whereHas('availableServicesRelation', static function (Builder $availableService) use ($column, $parameters) {
                $availableService->whereHas('service', static function (Builder $service) use ($column, $parameters) {
                    $column
                        ? $service->whereIn($column, $parameters)
                        : $service->where($parameters);
                });
        });
    }

    public static function networksByOperator(string $value)
    {
        return Network::whereHas('pointsOfSale', static function (Builder $pointsOfSale) use ($value) {
            $pointsOfSale->where('providerIdentifiers', 'like', "%\"$value\":%")
                ->has('users')->limit(1);
        });
    }
}
