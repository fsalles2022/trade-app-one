<?php

namespace TradeAppOne\Tests\Helpers;

use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Repositories\Collections\HierarchyRepository;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Http\Resources\PointOfSaleResource;

trait SaleHelper
{
    public function factorySales(int $quantity = 1, $user, $pointOfSale)
    {
        $saleRepository = new SaleRepository(app()->make(HierarchyRepository::class));
        foreach (range(1, $quantity) as $index) {
            $saleEntity = new Sale([
                'user' => $user->toArray(),
                'pointOfSale' => (new PointOfSaleResource())->map($pointOfSale),
            ]);
            $saleRepository->save($saleEntity);
        }
    }
}
