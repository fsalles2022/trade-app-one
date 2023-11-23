<?php


namespace Generali\tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class GeneraliNeoAPIFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test*/
    public function should_return_response_200_neo_api_products(): void
    {
        $user = (new UserBuilder())->build();
        
        $this->authAs($user)
        ->json('GET', 'generali/products')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment([
                'produto_parceiro_id' => '132',
                'produto_parceiro_id' => '130',
                'produto_parceiro_id' => '131'
            ]);
    }

    /** @test*/
    public function should_return_response_200_neo_api_plans(): void
    {
        $user = (new UserBuilder())->build();
        
        $this->authAs($user)
        ->json('GET', 'generali/plans', ['productPartnerId' => '132'])
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['produto_parceiro_plano_id' => '204']);
    }

    /** @test*/
    public function should_not_return_response_200_status_false_when_has_not_plan_neo_api(): void
    {
        $user = (new UserBuilder())->build();
        
        $this->authAs($user)
            ->json('GET', 'generali/plans', ['productPartnerId' => '0'])
            ->assertJsonFragment(['status' => false]);
    }
}
