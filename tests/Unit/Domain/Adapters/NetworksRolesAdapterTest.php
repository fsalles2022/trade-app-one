<?php

namespace TradeAppOne\Tests\Unit\Domain\Adapters;

use TradeAppOne\Domain\Adapters\NetworksRolesAdapter;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Tests\TestCase;

class NetworksRolesAdapterTest extends TestCase
{
    /** @test */
    public function should_return_collecion_with_networks_and_roles()
    {
        $network1 = factory(Network::class)->make(['id' => '1']);
        $network2 = factory(Network::class)->make(['id' => '2']);

        $networks = collect([$network1, $network2]);

        $role1 = factory(Role::class, 5)->make([
            'networkId' => $network1->id
        ]);

        $roles2 = factory(Role::class, 5)->make([
            'networkId' => $network2->id
        ]);

        $roles = $role1->concat($roles2);

        $adapter = (new NetworksRolesAdapter($roles, $networks))->adapt();

        $this->assertCount(2, $adapter);
        $this->assertArrayHasKey('id', $adapter[0]);
        $this->assertArrayHasKey('slug', $adapter[0]);
        $this->assertArrayHasKey('label', $adapter[0]);
        $this->assertArrayHasKey('cnpj', $adapter[0]);

        $this->assertArrayHasKey('id', $adapter[0]['roles'][0]);
        $this->assertArrayHasKey('name', $adapter[0]['roles'][0]);
        $this->assertArrayHasKey('slug', $adapter[0]['roles'][0]);
    }
}