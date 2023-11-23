<?php

namespace McAfee\Tests\Feature;

use McAfee\Models\McAfeeMobileSecurity;
use McAfee\Tests\Helpers\McAfeeFactoriesHelper;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class McAfeeFeatureTest extends TestCase
{
    use McAfeeFactoriesHelper, AuthHelper;
    private $userHelper;

    /** @test */
    public function plans_should_response_with_status_200_and_a_valid_structure()
    {
        $network  = factory(Network::class)->create(['slug' => 'tradeup-group']);
        $user     = (new UserBuilder())->withNetwork($network)->build();
        $response = $this->authAs($user)->get('sales/mcafee/plans');
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            [
                'id',
                'label',
                'product',
                'operation',
                'price',
                'installmentNumber',
                'quantity',
                'details'
            ]
        ]);
    }

    /** @test */
    public function cancel_should_response_with_status_200_and_a_exact_json()
    {
        $service = $this->mcAfeeFactories()->of(McAfeeMobileSecurity::class)->make(['status' => ServiceStatus::APPROVED]);
        (new SaleBuilder())->withServices([$service])->build();

        $response = $this->authAs($this->userHelper)
            ->put('sales/cancel', ['serviceTransaction' => $service->serviceTransaction]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertExactJson(
            ['message' => trans('mcAfee::messages.subscription.canceled', ['label' => $service->label])]
        );
    }

    protected function setUp()
    {
        parent::setUp();
        $this->userHelper = (new UserBuilder())->withPermission('SALE.CANCEL')->build();
    }
}
