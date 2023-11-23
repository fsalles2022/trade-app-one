<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Facades\UserPolicies;
use TradeAppOne\Exceptions\SystemExceptions\PointOfSaleExceptions;

class PointOfSaleRepository extends BaseRepository
{
    protected $model = PointOfSale::class;

    public function findOneWithFilters(array $where): ?PointOfSale
    {
        $model = $this->createModel();
        foreach ($where as $key => $value) {
            $model = $model->where($key, '=', $value);
        }
        return $model->first();
    }

    public function filter(array $parameters, User $user = null): Builder
    {
        $cnpjs        = self::pointsOfSaleAuthorized($user)->pluck('cnpj');
        $queryBuilder = $this->createModel()->newQuery()->whereIn('cnpj', $cnpjs);

        foreach ($parameters as $key => $value) {
            switch ($key) {
                case 'slug':
                    $queryBuilder->where('slug', '=', $value);
                    break;
                case 'state':
                    $queryBuilder->where('state', '=', $value);
                    break;
                case 'cnpj':
                    $queryBuilder->where('cnpj', 'like', "%$value%");
                    break;
                case 'networks':
                    $queryBuilder->whereHas('network', static function ($query) use ($value) {
                        $query->whereIn('slug', $value);
                    });
                    break;
                case 'hierarchies':
                    $queryBuilder->whereHas('hierarchy', static function ($query) use ($value) {
                        $query->whereIn('slug', $value);
                    });
                    break;
                case 'operator':
                    $queryBuilder->whereHas('availableServicesRelation.service', static function (Builder $builder) use ($value) {
                        $builder->whereIn('operator', array_wrap($value));
                    });
                    break;
            }
        }

        return $queryBuilder;
    }

    public function findOneByProviderIdentifiers($key, $code, $withRelations = false)
    {
        return $withRelations ?
                $this->model::whereRaw("JSON_EXTRACT(providerIdentifiers, '$.{$key}') = '{$code}'")
                    ->with([
                        'hierarchy:id,slug,label,sequence',
                        'network:id,slug,label,cnpj,companyName'
                    ])
                    ->first()
            :
                $this->model::whereRaw("JSON_EXTRACT(providerIdentifiers, '$.{$key}') = '{$code}'")
                    ->first();
    }

    public function findOneByCnpj(string $cnpj): ?PointOfSale
    {
        return $this->findOneBy('cnpj', $cnpj);
    }

    public function allPointsOfSaleWithNetwork(): Collection
    {
        return PointOfSale::with(['network:id,slug,label,cnpj', 'hierarchy:id,slug,label'])
            ->get(['id', 'slug', 'label', 'state', 'cnpj', 'zipCode','city', 'local','number', 'neighborhood',
                'complement','networkId', 'hierarchyId', 'providerIdentifiers'
            ]);
    }

    public function relationWithFilters($pointOfSale, $parameters): LengthAwarePaginator
    {
        return $this->filter($pointOfSale, $parameters)
        ->with('network', 'hierarchy')
        ->orderBy('pointsOfSale.slug', 'asc')
        ->paginate(10);
    }

    public static function findByProviderIdentifiers(string $value): Builder
    {
        $pointOfSale = PointOfSale::where('providerIdentifiers', 'like', "%{$value}%");
        throw_if($pointOfSale->get()->isEmpty(), PointOfSaleExceptions::pointOfSaleNotFound());

        return $pointOfSale;
    }

    public static function findByServiceOptions($actions, User $user = null): Builder
    {
        $authorizedIds = self::pointsOfSaleAuthorized($user)->pluck('id');

        return PointOfSale::query()->whereIn('id', $authorizedIds)
            ->whereHas('availableServicesRelation.options', static function (Builder $builder) use ($actions) {
                $builder->whereIn('action', array_wrap($actions));
            });
    }

    public static function pointsOfSaleAuthorized(User $user = null): Collection
    {
        return $user
            ? UserPolicies::setUser($user)->getPointsOfSaleAuthorized()
            : UserPolicies::getPointsOfSaleAuthorized();
    }

    public static function availableServiceRelation(?array $parameters, string $column = null): Builder
    {
        return PointOfSale::query()
            ->whereHas('availableServicesRelation', static function (Builder $availableService) use ($column, $parameters) {
                $availableService->whereHas('service', static function (Builder $service) use ($column, $parameters) {
                    $column
                        ? $service->where($column, $parameters)
                        : $service->where($parameters);
                });
            });
    }
}
