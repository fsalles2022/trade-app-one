<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\HierarchyRepository;
use TradeAppOne\Exceptions\SystemExceptions\HierarchyExceptions;

class HierarchyService extends BaseService
{
    private $repository;

    public function __construct(HierarchyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getNetworksThatBelongsToUser(User $user): Collection
    {
        return $this->repository->getNetworksThatBelongsToUser($user);
    }

    public function getPointsOfSaleThatBelongsToUser(User $user): Collection
    {
        return $this->repository->getPointsOfSaleThatBelongsToUser($user);
    }

    public function findOneHierarchyBySlug(string $slug): ?Hierarchy
    {
        $hierarchy = $this->repository->findOneBy('slug', $slug);
        throw_if($hierarchy === null, HierarchyExceptions::notFound());

        return $hierarchy;
    }

    public function getHierarchiesOfNetwork(Network $network): ?Collection
    {
        return $this->repository->where('networkId', $network->id)->where('slug', '!=', $network->slug)->get();
    }

    public function getPointsOfSaleOfHierarchyTopToBottom(Hierarchy $hierarchy): Collection
    {
        return $this->repository->getPointsOfSaleHierarchyTopToBottom($hierarchy);
    }

    public function getChildrenHierarchiesOfHierarchy(Hierarchy $hierarchy): Collection
    {
        return $this->repository->getChildrenHierarchiesOfHierarchy($hierarchy);
    }

    public function hierarchiesThatUserHasAuthority(User $user): Collection
    {
        $myHierarchies       = $user->hierarchies()->with('network')->get();
        $childrenHierarchies = $this->repository->getChildrenHierarchies($myHierarchies);

        return $childrenHierarchies;
    }

    public function hierarchiesThatUserHasAuthorityPaginated(User $user, $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $myHierarchies = $user->hierarchies()->with('network')->get();
        return $this->repository->getChildrenHierarchiesFromPaginated($myHierarchies, $filters);
    }
}
