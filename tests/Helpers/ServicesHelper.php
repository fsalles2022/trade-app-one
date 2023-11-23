<?php

namespace TradeAppOne\Tests\Helpers;

use TradeAppOne\Domain\Factories\SaleFactory;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;

class ServicesHelper
{
    use MobileSecurityHelper, RouboFurtoHelper;

    public function generateRandomSalesWithServices(int $quantity, $userHelper)
    {
        $this->factorySalesWithMobileSecurityService($quantity, $userHelper['user'], $userHelper['pointOfSale']);
    }

    public function factorySalesWithMobileSecurityService(int $quantity, $user, $pointOfSale)
    {
        $this->factoryServices($quantity, $user, $pointOfSale, [$this->getMobileSecurityFilled()]);
    }

    public function factoryServices(int $quantity = 1, $user, $pointOfSale, $servicePayload)
    {
        foreach (range(1, $quantity) as $index) {
            $saleEntity     = SaleFactory::make($user, $pointOfSale, $servicePayload);
            $saleRepository = (new SaleRepository())->save($saleEntity);
        }
    }

    public function factorySalesWithRouboFurtoService(int $quantity, $user, $pointOfSale)
    {
        $this->factoryServices($quantity, $user, $pointOfSale, [$this->getRouboFurtoFilled()]);
    }
}
