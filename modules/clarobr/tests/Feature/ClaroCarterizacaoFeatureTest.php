<?php

namespace ClaroBR\Tests\Feature;

use ClaroBR\Models\ControleBoleto;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use Reports\Tests\Helpers\BindInstance;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroCarterizacaoFeatureTest extends TestCase
{
    use AuthHelper, BindInstance, SivFactoriesHelper, SivBindingHelper;

    /** @test */
    public function should_save_sale_and_associate_a_user(): void
    {
        $this->bindSivResponse();
        $this->bindMountNewAttributesFromSiv();

        $network         = (new NetworkBuilder())->build();
        $pointOfSale     = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user            = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $userAssociative = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $service         = $this->sivFactories()->of(ControleBoleto::class)->make()->toArray();
        $salePayload     = $this->payload($service);

        $this->authAs($user)
            ->withHeader('client', SubSystemEnum::WEB)
            ->post('/sales', $salePayload)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('sales', [
                'user.cpf'             => $userAssociative->cpf,
                'user.associative.cpf' => $user->cpf,
            ], 'mongodb');
    }

    private function payload(array $service): array
    {
        return [
            'pointOfSale' => 1,
            'associateUserId' => 2,
                'services' => [$service]
        ];
    }
}
