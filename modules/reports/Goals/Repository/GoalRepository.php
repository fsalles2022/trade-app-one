<?php

namespace Reports\Goals\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Reports\Goals\Models\Goal;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;
use TradeAppOne\Domain\Repositories\Collections\HierarchyRepository;

class GoalRepository extends BaseRepository
{
    protected $model = Goal::class;
    protected $hierarchyRepository;

    public function __construct(HierarchyRepository $hierarchyRepository)
    {
        $this->hierarchyRepository = $hierarchyRepository;
    }

    public function filterAndPaginate(Collection $pointsOfSaleCollection, array $parameters, int $perPage = 10)
    {

        $pdvIds       = $pointsOfSaleCollection->pluck('id');
        $queryBuilder = Goal::query();

        foreach ($parameters as $key => $value) {
            switch ($key) {
                case 'slug':
                    $queryBuilder->whereHas('pointOfSale', function ($query) use ($value) {
                        $query->where('slug', $value);
                    });
                    break;

                case 'cnpj':
                    $queryBuilder->whereHas('pointOfSale', function ($query) use ($value) {
                        $query->where('cnpj', $value);
                    });
            }
        }

        return $queryBuilder
            ->whereIn('pointOfSaleId', $pdvIds)
            ->with('pointOfSale', 'goalType')
            ->orderBy('createdAt', 'desc')
            ->paginate($perPage);
    }

    public function getGoalsBasedPointsOfSaleAndMonths(array $filters, array $types): Collection
    {
        return Goal::query()
            ->where('year', $filters['year'])
            ->whereIn('month', $filters['months'])
            ->whereHas('pointOfSale', function ($pdv) use ($filters) {
                $pdv->whereIn('networkId', $filters['networks'])
                    ->whereIn('cnpj', $filters['cnpjs']);
            })->whereHas('goalType', function ($query) use ($types) {
                $query->where('type', $types);
            })
            ->with('pointOfSale')
            ->get();
    }

    public function findByCollectionPointOfSale($pointsOfSaleIds, int $month, int $year, $types): Builder
    {
        return Goal::query()
            ->whereIn('pointOfSaleId', array_wrap($pointsOfSaleIds))
            ->where('month', $month)
            ->where('year', $year)
            ->whereHas('goalType', function ($query) use ($types) {
                $query->whereIn('type', array_wrap($types));
            });
    }
}
