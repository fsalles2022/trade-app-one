<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;

class PointOfSaleRepositoryReader extends BaseRepository
{
    protected $hierarchyRepository;
    protected $pointOfSaleRepository;

    protected $model = PointOfSale::class;

    public function __construct(HierarchyRepository $hierarchyRepository, PointOfSaleRepository $pointOfSaleRepository)
    {
        $this->hierarchyRepository   = $hierarchyRepository;
        $this->pointOfSaleRepository = $pointOfSaleRepository;
    }

    public function getPointOfSaleExport(User $user, array $parameters = [])
    {
        return $this->pointOfSaleRepository->filter($parameters, $user)
            ->with('network:id,slug', 'hierarchy:id,slug')
            ->get();
    }
}
