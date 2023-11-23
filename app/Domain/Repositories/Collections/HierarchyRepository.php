<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\User;

class HierarchyRepository extends BaseRepository
{
    protected $pointOfSaleRepository;

    protected $model = Hierarchy::class;

    public function __construct(PointOfSaleRepository $pointOfSaleRepository)
    {
        $this->pointOfSaleRepository = $pointOfSaleRepository;
    }

    public function getNetworksThatBelongsToUser(User $user): Collection
    {
        $hierarchiesAttachedToUser = $user->hierarchies()->get();

        if ($this->existsHierarchyWithParentNull($hierarchiesAttachedToUser)) {
            return Network::all();
        }

        $childrenHierarchies        = $this->getChildrenHierarchies($hierarchiesAttachedToUser)->unique('networkId');
        $networks                   = $this->getNetworksFromHierarchies($childrenHierarchies);
        $pointsOfSaleAttachedToUser = $user->pointsOfSale()->get();
        $networksAttachedToUser     = $this->getNetworksFromPointsOfSale($pointsOfSaleAttachedToUser);

        return $networks->merge($networksAttachedToUser)->unique('id');
    }

    private function existsHierarchyWithParentNull(Collection $hierarchies): bool
    {
        return $hierarchies->contains(static function ($hierarchy) {
            return $hierarchy->parent === null;
        });
    }

    public function getChildrenHierarchies(Collection $hierarchies): Collection
    {
        $childrenHierarchies = new Collection();

        foreach ($hierarchies as $actualHierarchy) {
            $sequence            = $actualHierarchy->sequence;
            $children            = $this->getChildrenHierarchiesFrom($sequence);
            $childrenHierarchies = $childrenHierarchies->merge($children);
        }

        return $childrenHierarchies->merge($hierarchies)->unique('id');
    }

    private function getChildrenHierarchiesFrom(string $sequence): Collection
    {
        return $this->createModel()
            ->where('sequence', 'like', "{$sequence}.%")
            ->with('network')
            ->get();
    }

    private function getNetworksFromHierarchies(Collection $hierarchies): Collection
    {
        $networks = new Collection();

        foreach ($hierarchies as $actualHierarchy) {
            $network = $actualHierarchy->network()->first();
            if ($network) {
                $networks->push($network);
            }
        }
        return $networks;
    }

    private function getNetworksFromPointsOfSale(Collection $pointsOfSale): Collection
    {
        $networks = new Collection();

        foreach ($pointsOfSale as $pointOfSale) {
            $network = $pointOfSale->network()->first();
            if ($network) {
                $networks->push($network);
            }
        }
        return $networks;
    }

    public function getPointsOfSaleThatBelongsToUser(User $user): Collection
    {
        $hierarchiesAttachedToUser = $user->hierarchies()->get();

        if ($this->existsHierarchyWithParentNull($hierarchiesAttachedToUser)) {
            return $this->pointOfSaleRepository->allPointsOfSaleWithNetwork();
        }

        $childrenHierarchies               = $this->getChildrenHierarchies($hierarchiesAttachedToUser);
        $mergedHierarchies                 = $hierarchiesAttachedToUser->merge($childrenHierarchies);
        $pointsOfSaleAttachedToHierarchies = $this->getPointsOfSaleFromHierarchies($mergedHierarchies);
        $pointsOfSaleAttachedToUser        = $user->pointsOfSale()->with('network')->get();

        return $pointsOfSaleAttachedToHierarchies->merge($pointsOfSaleAttachedToUser)->unique('id');
    }

    public function getPointsOfSaleFromHierarchies(Collection $hierarchies): Collection
    {
        $pointsOfSale = new Collection();

        foreach ($hierarchies as $actualHierarchy) {
            $actualPointsOfSale = $actualHierarchy->pointsOfSale()->with('network')->get();
            $pointsOfSale       = $pointsOfSale->merge($actualPointsOfSale)->unique('id');
        }

        return $pointsOfSale;
    }

    public function getPointsOfSaleHierarchyTopToBottom(Hierarchy $hierarchy): Collection
    {
        $pointsOfSale    = new Collection();
        $allPointsOfSale = $hierarchy->pointsOfSale()->with('network')->get();
        $pointsOfSale    = $pointsOfSale->merge($allPointsOfSale)->unique('id');
        $children        = $this->getChildrenHierarchies(collect([$hierarchy]));
        if ($children) {
            foreach ($children as $actualHierarchy) {
                $actualPointsOfSale = $actualHierarchy->pointsOfSale;
                $pointsOfSale       = $pointsOfSale->merge($actualPointsOfSale)->unique('id');
            }
        }
        return $pointsOfSale;
    }

    public function getChildrenHierarchiesOfHierarchy(Hierarchy $hierarchy): Collection
    {
        return $this->where('parent', $hierarchy->id)->get();
    }

    public function getChildrenHierarchiesFromPaginated(Collection $hierarchies, $filters = []): LengthAwarePaginator
    {
        $builder = Hierarchy::query();
        foreach ($hierarchies as $hierarchy) {
            $builder = $builder->orWhere('sequence', 'like', "{$hierarchy->sequence}.%");
        }

        foreach ($filters as $filter => $value) {
            if ($filter == 'networks') {
                $builder->whereHas('network', function ($query) use ($value) {
                    $query->whereIn('slug', $value);
                });
            }
        }

        return $builder->with('network:id,slug,label')->paginate(10);
    }
}
