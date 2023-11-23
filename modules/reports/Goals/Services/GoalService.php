<?php

namespace Reports\Goals\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Reports\Goals\Models\Goal;
use Reports\Goals\Repository\GoalRepository;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Services\BaseService;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;

class GoalService extends BaseService
{
    protected $goalRepository;
    protected $pointOfSaleRepository;
    protected $hierarchyService;

    public function __construct(
        GoalRepository $repository,
        PointOfSaleRepository $pointOfSaleRepository,
        HierarchyService $hierarchyService
    ) {
        $this->goalRepository        = $repository;
        $this->pointOfSaleRepository = $pointOfSaleRepository;
        $this->hierarchyService      = $hierarchyService;
    }

    public function persist(array $attributes)
    {
        $month           = data_get($attributes, 'month');
        $year            = data_get($attributes, 'year');
        $pointOfSaleCnpj = data_get($attributes, 'cnpj');
        $goalTypeId      = data_get($attributes, 'goalTypeId');

        $pointOfSale = $this->pointOfSaleRepository->findOneBy('cnpj', $pointOfSaleCnpj);

        if ($pointOfSale) {
            $attributes['pointOfSaleId'] = $pointOfSale->id;
            $alreadyExistsGoal           = $this->goalRepository
                ->where('month', $month)
                ->where('year', $year)
                ->where('pointOfSaleId', $pointOfSale->id)
                ->where('goalTypeId', $goalTypeId)
                ->first();

            if ($alreadyExistsGoal) {
                return $this->update($alreadyExistsGoal->id, $attributes);
            }

            return $this->create($attributes);
        }
        throw new PointOfSaleNotFoundException();
    }

    public function update(int $id, array $attributes): ?Goal
    {
        $goal = $this->goalRepository->where('id', $id)->first();
        if ($goal instanceof Goal) {
            return $this->goalRepository->update($goal, $attributes);
        }
        return null;
    }

    public function create(array $attributes): Goal
    {
        return $this->goalRepository->create($attributes);
    }

    public function fetch(int $id)
    {
        return $this->goalRepository->find($id);
    }

    public function fetchWithContext($parameters = [])
    {
        $user         = $this->userService->getAuthenticatedUser();
        $poinstOfSale = $this->hierarchyService->getPointsOfSaleThatBelongsToUser($user);
        return $this->goalRepository->filterAndPaginate($poinstOfSale, $parameters);
    }

    public function findByPointOfSale(PointOfSale $pointOfSale, Carbon $date, $types)
    {
        $pointOfSaleId = $pointOfSale->id;
        $month         = $date->month;
        $year          = $date->year;

        return $this->goalRepository->findByCollectionPointOfSale($pointOfSaleId, $month, $year, $types)->first();
    }

    public function findByCollectionPointOfSale(Collection $pointsOfSale, Carbon $date, $types): Collection
    {
        $pointsOfSaleIds = $pointsOfSale->pluck('id')->toArray();
        $month           = $date->month;
        $year            = $date->year;

        return $this->goalRepository->findByCollectionPointOfSale($pointsOfSaleIds, $month, $year, $types)->get();
    }
}
