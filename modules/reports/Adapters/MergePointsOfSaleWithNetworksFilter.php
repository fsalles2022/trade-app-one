<?php

namespace Reports\Adapters;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Adapters\Adapter;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;

class MergePointsOfSaleWithNetworksFilter implements Adapter
{
    private $networks;
    private $hierarchies;
    private $pointOfSales;

    public function __construct(Collection $networks, Collection $hierarchies, Collection $pointOfSales)
    {
        $this->networks     = $networks->sortBy('label');
        $this->hierarchies  = $hierarchies;
        $this->pointOfSales = $pointOfSales;
    }

    public function adapt(): array
    {
        $networks = [];

        foreach ($this->networks as $network) {
            $networks[$network->slug] = [
                'id'    => $network->slug,
                'label' => $network->label
            ];

            $hierarchies                             = $this->getHierarchies($network);
            $networks[$network->slug]['hierarchies'] = array_values($hierarchies);
        }

        return array_values($networks);
    }

    public function getHierarchies(Network $network): array
    {
        $hierarchies       = [];
        $hierarchiesEntity = $this->hierarchies->where('networkId', '=', $network->id)->sortBy('label');

        foreach ($hierarchiesEntity as $hierarchy) {
            $hierarchies[$hierarchy->slug] = [
                'id'    => $hierarchy->slug,
                'label' => $hierarchy->label
            ];

            $pointsOfSales                                 = $this->getPointOfSales($hierarchy);
            $hierarchies[$hierarchy->slug]['pointsOfSale'] = $pointsOfSales;
        }

        return array_values($hierarchies);
    }

    private function getPointOfSales(Hierarchy $hierarchy): array
    {
        $pointOfSales      = [];
        $pointOfSaleEntity = $this->pointOfSales->where('hierarchyId', '=', $hierarchy->id)->sortBy('label');

        foreach ($pointOfSaleEntity as $pointOfSale) {
            $pointOfSales[$pointOfSale->slug] = [
                'id'    => $pointOfSale->cnpj,
                'label' => $pointOfSale->label
            ];
        }

        return array_values($pointOfSales);
    }
}
