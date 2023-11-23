<?php

namespace TradeAppOne\Exports\Operators\Traits;

use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Models\Tables\User;

trait OperatorsFilter
{
    public function filter(array $parameters, string $operations): Builder
    {
        $queryScope = User::query()->whereHas('pointsOfSale', static function ($queryPointOfSale) use ($operations) {
            $queryPointOfSale->where('providerIdentifiers', 'like', "%$operations%");
        });
        foreach ($parameters as $key => $value) {
            switch ($key) {
                case 'status':
                    $queryScope = $queryScope->whereIn('activationStatusCode', $value);
                    break;
                case 'roles':
                    $queryScope = $queryScope->whereHas('role', function ($roleQuery) use ($value) {
                        $roleQuery->whereIn('slug', array_wrap($value));
                    });
                    break;
                case 'networks':
                    $queryScope = $queryScope->whereHas('pointsOfSale.network', function ($networkQuery) use ($value) {
                        $networkQuery->whereIn('slug', array_wrap($value));
                    });
                    break;
                default:
                    $queryScope = $queryScope = User::query();
            }
        }
        return $queryScope
            ->with('role')
            ->with('pointsOfSale')
            ->with('pointsOfSale.network');
    }
}
