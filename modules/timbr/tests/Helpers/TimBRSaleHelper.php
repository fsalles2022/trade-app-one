<?php

namespace TimBR\Tests\Helpers;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

trait TimBRSaleHelper
{
    public function utilsForUnitTests()
    {
        $user = new User([
            "name"           => "123",
            "lastName"       => "Resto",
            "cpf"            => "123",
            "email"          => "123",
            "areaCodePrefix" => "123",
        ]);
        $role = new Role(['slug' => "132"]);
        $user->setRelation('role', $role);

        $pointOfSale     = $this->getPointOfSale();
        $pointOfSale->id = 2;
        $user->setRelation('pointOfSale', $this->getPointOfSale());

        $token                   = "Bearer 18723gb";
        $resource['user']        = $user;
        $resource['token']       = $token;
        $resource['role']        = $user->role;
        $resource['pointOfSale'] = $pointOfSale;
        return $resource;
    }

    public function getPointOfSale()
    {
        $network     = new Network(['label' => 'rede Teste', 'slug' => 'rede']);
        $pointOfSale = new PointOfSale([
            "label"                  => "a",
            "cnpj"                   => "a",
            "areaCode"               => "11",
            "state"                  => "SP",
            "tradingName"            => "a",
            "companyName"            => "a",
            "providerIdentifier"     => json_encode([Operations::TIM => 'SP_TEST_CUSTCODE']),
        ]);

        return $pointOfSale->setRelation('network', $network);
    }
}
