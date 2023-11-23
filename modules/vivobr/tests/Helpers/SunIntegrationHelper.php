<?php

namespace VivoBR\Tests\Helpers;

use Illuminate\Database\Eloquent\Factory;
use MongoDB\BSON\ObjectId;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

trait SunIntegrationHelper
{
    public function getUserWithSunCredentials(): User
    {
        $user = factory(User::class)->make();
        $role = factory(Role::class)->states('admin')->make();
        $user->setRelation('role', $role);
        $user->setRelation('pointOfSale', $this->getPointOfSaleWithSunIdentifiers());
        return $user;
    }

    public function getPointOfSaleWithSunIdentifiers(): PointOfSale
    {
        $network     = factory(Network::class)->make();
        $pointOfSale = factory(PointOfSale::class)->make();

        $pointOfSale->_id = new ObjectId();
        $pointOfSale->setRelation('network', $network);
        $network->setRelation('pointsOfSale', $pointOfSale);

        return $pointOfSale;
    }

    public function getClearUserWithSunCredentials(): User
    {
        $user = factory(User::class)->make();
        $role = factory(Role::class)->states('admin')->make();
        $user->setRelation('role', $role);
        return $user;
    }

    public function factory(): Factory
    {
        return Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/vivobr/Factories/')
        );
    }
}
