<?php

namespace ClaroBR\Tests\Helpers;

use ClaroBR\Connection\SivHttpClient;
use ClaroBR\SivHeaders;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Crypt;
use MongoDB\BSON\ObjectId;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

trait SivIntegrationHelper
{
    public function getUserWithSivCredentials(): User
    {
        $user = factory(User::class)->make();
        $role = factory(Role::class)->states('admin')->make();
        $user->setRelation('role', $role);
        $user->integrationCredentials = [
            'siv' => [
                'id' => '2',
                'cpf' => '14212',
                'password' => Crypt::encryptString('asda')
            ]
        ];
        $user->setRelation('pointOfSale', $this->getPointOfSaleWithSivIdentifiers());
        $user->setRelation('role', $role);
        return $user;
    }

    public function getPointOfSaleWithSivIdentifiers(): PointOfSale
    {
        $network     = factory(Network::class)->make();
        $pointOfSale = factory(PointOfSale::class)->make();

        $pointOfSale->_id                 = new ObjectId();
        $pointOfSale->providerIdentifiers = json_encode(["siv" => 2]);
        $pointOfSale->setRelation('network', $network);
        $network->setRelation('pointsOfSale', $pointOfSale);

        return $pointOfSale;
    }

    public function getClearUserWithSivCredentials(): User
    {
        $user = factory(User::class)->make();
        $role = factory(Role::class)->states('admin')->make();
        $user->setRelation('role', $role);
        $user->integrationCredentials = [
            'siv' => [
                'id' => '2',
                'cpf' => '14212',
                'password' => Crypt::encryptString('asda')
            ]
        ];
        return $user;
    }

    public function getPointOfSaleNoSivIdentifiers(): PointOfSale
    {
        $network     = factory(Network::class)->make();
        $pointOfSale = factory(PointOfSale::class)->make();

        $pointOfSale->_id = new ObjectId();
        $pointOfSale->setRelation('network', $network);
        $network->setRelation('pointsOfSale', $pointOfSale);

        return $pointOfSale;
    }

    public function getUserNoSivCredentials(): User
    {
        $user = factory(User::class)->make();
        $role = factory(Role::class)->states('admin')->make();
        $user->setRelation('role', $role);
        $user->setRelation('pointOfSale', $this->getPointOfSaleWithSivIdentifiers());
        $user->setRelation('role', $role);
        return $user;
    }

    public function mockSivEndpointSaleResponse()
    {
        $expected = $this->getSaleResponseStructure();
        app()->bind(SivHttpClient::class, function () use ($expected) {
            $prop = app()->make(SivHeaders::class);
            $mock = new MockHandler([
                new Response(200, ['X-Foo' => 'Bar'], ['ok' => '1']),
            ]);

            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);

            return new SivHttpClient($client);
        });
    }

    public function getSaleResponseStructure()
    {
        return [
            "type" => "success",
            "message" => "Venda realizada",
            "data" => [
                'venda' => [
                    'id',
                    'pos' => [],
                    'services' => [
                        'id'
                    ]
                ]
            ]
        ];
    }
}
